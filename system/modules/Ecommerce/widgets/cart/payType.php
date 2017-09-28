<div class="row">
    <div class="col-md-4">
        <ul class="nav nav-pills nav-stacked">
            <?php
            $hiddenId = Tools::randomString();
            foreach ($payTypes as $payType) {
                if ((!empty($_POST['payType']) && $_POST['payType'] == $payType->id) || ($cart->payType && $payType->id == $cart->payType->id)) {
                    $checked = 'checked';
                } else {
                    $checked = '';
                }
                echo '<li' . ($checked ? ' class="active"' : '') . '><a href = "#" onclick = "document.getElementById(\'' . $hiddenId . '\').value=\'' . $payType->id . '\';inji.Ecommerce.Cart.calcSum();return false;">';
                echo $payType->name;
                echo '</a></li>';
            }
            $form->input('hidden', "payType", '', [
                'value' => $cart->paytype_id,
                'attributes' => [
                    'id' => $hiddenId
                ],
            ]);
            ?>
        </ul>
    </div>
    <div class="col-md-8">
        <?php
        if ($cart->payType) {
            echo "<h4>Информация об оплате</h4>";
            echo $cart->payType->info;
        } else {
            echo "<h4>Выберите способ оплаты</h4>";
        }
        ?>
    </div>
</div>