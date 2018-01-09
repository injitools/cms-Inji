<?php
$count = count($item->infos);
if ($count > 6) {
    ?>
    <a class="btn btn-xs btn-primary" onclick='inji.Ui.dataManagers.popUp("Ecommerce\\Cart:<?= $item->id; ?>",
            {
            "limit": "30",
            "page": "1",
            "categoryPath": "\/", "filters": {
            "cart_status_id": {"value": ["2"]
            },
            "delivery_id": {"value": []},
            "payed": {"value": ""},
            "complete_data": {"min": "", "max": ""}},
            "sortered": {"9": "desc"},
            "mode": "",
            "all": false,
            "relation": "infos"}
            )'><?= $count . ' ' . Tools::getNumEnding($count, ['Элемент', 'Элемента', 'Элементов']); ?></a>
    <?php
} else {
    foreach ($item->infos as $info) {
        echo \Inji\Model::resloveTypeValue($info, 'value') . ' ';
    }
}
?>