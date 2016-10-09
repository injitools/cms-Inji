<?php

/**
 * Money sums comparator
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Money;

class Sums extends \Object {

  public $sums = [];

  public function __construct($sums) {
    $this->sums = $sums;
  }

  function plus(Sums $sums) {
    $newSums = $this->sums;
    foreach ($sums->sums as $currency_id => $sum) {
      $newSums[$currency_id] = isset($newSums[$currency_id]) ? $newSums[$currency_id] + $sum : $sum;
    }
    return new Sums($newSums);
  }

  function minus(Sums $sums) {
    $newSums = $this->sums;
    foreach ($sums->sums as $currency_id => $sum) {
      $newSums[$currency_id] = isset($newSums[$currency_id]) ? $newSums[$currency_id] - $sum : -$sum;
    }
    return new Sums($newSums);
  }

//Equal, Less and Greater
  public function greater(Sums $sums) {
    if (count($this->sums) == count($sums->sums) && isset($sums->sums[key($this->sums)])) {
      return current($this->sums) > current($sums->sums);
    }
  }

  public function equal(Sums $sums) {
    if (count($this->sums) == count($sums->sums) && isset($sums->sums[key($this->sums)])) {
      return current($this->sums) == current($sums->sums);
    }
  }

  function __toString() {
    $string = '';
    $first = true;
    foreach ($this->sums as $currency_id => $sum) {
      if ($first) {
        $first = false;
      } else {
        $string.= '<br />';
      }
      $string.= '<span style="white-space:nowrap;">';
      $string.= number_format($sum, 2, '.', ' ');
      if (\App::$cur->money) {
        $currency = \Money\Currency::get($currency_id);
        if ($currency) {
          $string.= ' ' . $currency->acronym();
        } else {
          $string.= ' руб.';
        }
      } else {
        $string.= ' руб.';
      }
      $string.= '</span>';
    }
    return $string;
  }

}
