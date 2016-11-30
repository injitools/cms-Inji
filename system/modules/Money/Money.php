<?php

/**
 * Money module
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class Money extends Module {

    public $currentMerchant = '';

    public function init() {
        if (!empty($this->config['defaultMerchant'])) {
            $this->currentMerchant = $this->config['defaultMerchant'];
        }
    }

    public function refillPayRecive($data) {
        $wallets = $this->getUserWallets($data['pay']->user_id);
        foreach ($wallets as $wallet) {
            if ($wallet->currency_id == $data['pay']->currency_id) {
                $wallet->diff($data['pay']->sum, 'Пополнение');
                break;
            }
        }
    }

    public function goToMerchant($pay, $merchant, $method, $merchantOptions) {
        $objectName = $merchant->object_name;

        if (is_array($pay)) {
            $pay = new Money\Pay($pay);
            $pay->save();
        }
        switch ($method['type']) {
            case 'transfer':
                $sum = $pay->sum / $method['transfer']->rate;
                break;
            default:
                $sum = $pay->sum;
        }
        $className = 'Money\MerchantHelper\\' . $objectName;
        return $className::goToMerchant($pay->id, $sum, $method['currency'], $merchantOptions['description'], $merchantOptions['success'], $merchantOptions['false']);
    }

    public function reciver($data, $system, $status, $mr) {
        if ($system) {
            $merchant = \Money\Merchant::get($system, 'object_name');
        } else {
            $merchant = false;
        }
        if ($merchant) {
            $this->currentMerchant = $system;
        }
        $className = 'Money\MerchantHelper\\' . $this->currentMerchant;
        $result = $className::reciver($data, $status);
        $result['pay'] = null;
        if (!empty($result['payId'])) {
            $result['pay'] = Money\Pay::get($result['payId']);
            $mr->pay_id = $result['payId'];
        }
        if ($result['pay'] && $result['pay']->pay_status_id == 1) {
            $statuses = \Money\Pay\Status::getList(['key' => 'code']);
            if (!empty($statuses[$result['status']])) {
                $result['pay']->pay_status_id = $statuses[$result['status']]->id;
            }
            $result['pay']->date_recive = date('Y-m-d H:i:s');
            $result['pay']->save();
            if ($result['status'] == 'success' && $result['pay']->callback_module && $result['pay']->callback_method) {
                App::$cur->{$result['pay']->callback_module}->{$result['pay']->callback_method}($result);
            }
        }
        if (!empty($result['callback'])) {
            echo $result['callback'];
            $mr->result_callback = $result['callback'];
        }
        if (!empty($result['status'])) {
            $mr->status = $result['status'];
        }
        $mr->save();
    }

    public function getUserBlocks($userId = null) {
        $userId = $userId ? $userId : \Users\User::$cur->id;
        $blocked = \Money\Wallet\Block::getList(['where' => [
                        ['wallet:user_id', $userId],
                        [
                            ['date_expired', '0000-00-00 00:00:00'],
                            ['date_expired', date('Y-m-d H:i:s'), '>', 'OR']
                        ]
        ]]);
        $blocks = [];
        foreach ($blocked as $block) {
            if (empty($blocks[$block->wallet->currency_id])) {
                $blocks[$block->wallet->currency_id] = $block->amount;
            } else {
                $blocks[$block->wallet->currency_id] += $block->amount;
            }
        }
        return $blocks;
    }

    public function getUserWallets($userId = null, $walletIdasKey = false, $forSelect = false, $transferOnly = false) {
        $userId = $userId ? $userId : \Users\User::$cur->id;
        if (!$userId) {
            return [];
        }
        $this->getUserBlocks($userId);
        $where = [['wallet', 1]];
        if ($transferOnly) {
            $where[] = ['transfer', 1];
        }
        $currencies = Money\Currency::getList(['where' => $where]);
        $wallets = Money\Wallet::getList(['where' => ['user_id', $userId], 'key' => 'currency_id']);
        $result = [];
        foreach ($currencies as $currency) {
            if (empty($wallets[$currency->id])) {
                $wallet = new Money\Wallet();
                $wallet->user_id = $userId;
                $wallet->currency_id = $currency->id;
                $wallet->save();
                $result[$walletIdasKey ? $wallet->id : $currency->id] = $forSelect ? $wallet->name() : $wallet;
            } else {
                $result[$walletIdasKey ? $wallets[$currency->id]->id : $currency->id] = $forSelect ? $wallets[$currency->id]->name() : $wallets[$currency->id];
            }
        }
        return $result;
    }

    public function rewardTrigger($event) {
        $triggers = Money\Reward\Trigger::getList(['where' => [['type', 'event'], ['value', $event['eventName']]]]);
        foreach ($triggers as $trigger) {
            $handlers = $this->getSnippets('rewardTriggerHandler');
            if (!empty($handlers[$trigger->handler])) {
                $handlers[$trigger->handler]['handler']($event['eventObject'], $trigger);
            }
        }
        return $event['eventObject'];
    }

    public function rewardConditionTrigger($event) {
        $items = Money\Reward\Condition\Item::getList(['where' => [['type', 'event'], ['value', $event['eventName']]]]);
        foreach ($items as $item) {
            $recivers = $this->getSnippets('rewardConditionItemReciver');
            if (!empty($recivers[$item->reciver])) {
                $recivers[$item->reciver]['reciver']($event['eventObject'], $item);
                foreach ($item->condition->rewards as $reward) {
                    if ($reward->block) {
                        $reward->checkBlocked();
                    }
                }
            }
        }
        return $event['eventObject'];
    }

    public function reward($reward_id, $sums = [], $rootUser = null) {
        $rootUser = $rootUser ? $rootUser : \Users\User::$cur;
        $reward = \Money\Reward::get($reward_id);
        if (!$reward->active) {
            return false;
        }
        $reward->checkBlocked();
        $reward_count = \Money\Reward\Recive::getCount(['where' => ['reward_id', $reward_id]]);
        if ($reward_count >= $reward->quantity && $reward->quantity) {
            return false;
        }
        $types = $this->getSnippets('rewardType');
        $checkers = $this->getSnippets('userActivity');
        foreach ($reward->levels(['order' => ['level', 'asc']]) as $level) {
            $user = $rootUser;
            for ($i = 0; $i < $level->level; $i++) {
                $next = $user && $user->parent ? $user->parent : false;
                if (!$next && $reward->lasthaveall) {
                    break;
                }
                $noActive = $next->blocked;
                foreach ($checkers as $checker) {
                    if ($noActive) {
                        break;
                    }
                    $noActive = !$checker['checker']($next);
                }
                if ($next && $next->parent_id && $noActive) {
                    foreach ($next->users as $childUser) {
                        $childUser->parent_id = $next->parent_id;
                        $childUser->save();
                    }
                    $i--;
                    $user = Users\User::get($user->id);
                    $rootUser = Users\User::get($rootUser->id);
                    continue;
                }
                $user = $next;
            }
            if (!$user) {
                continue;
            }

            if ($reward->peruser) {
                $recives = \Money\Reward\Recive::getList(['where' => [['user_id', $user->id], ['reward_id', $reward->id]]]);
                $amount = 0;
                foreach ($recives as $recive) {
                    $amount += $recive->amount;
                }
                if ($amount >= $reward->peruser) {
                    continue;
                }
            }
            $rewardGet = true;
            if (!$level->nocondition) {
                foreach ($reward->conditions as $condition) {
                    if (!$condition->checkComplete($user->id)) {
                        $rewardGet = false;
                        break;
                    }
                }
                if (!$rewardGet && !$reward->block) {
                    continue;
                }
            }
            $recive = new \Money\Reward\Recive();
            $recive->reward_id = $reward->id;
            $recive->user_id = $user->id;
            $recive->amount = 1;
            $recive->save();
            $count = $types[$level->type]['rewarder']($reward, $sums, $user, $rootUser, $level, $rewardGet);
        }
    }

}
