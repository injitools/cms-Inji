<div class="users">
    <div class="content">
        <h2>Введите E-Mail для восстановления пароля</h2>
        <?php
        $form = new \Ui\Form();
        $form->method = 'GET';
        $form->begin();

        $form->input('email', 'user_mail', 'E-Mail', ['placeholder' => 'mail@mail.ru', 'required' => true]);
        $form->input('hidden', 'passre', '', ['value' => 1]);
        $form->end('Восстановить');
        ?>
    </div>
</div>