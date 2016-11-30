<?php
if (!empty($_POST['partnerInvite']['email'])) {
    $title = '';
    if (!empty($_POST['partnerInvite']['name'])) {
        $title .= 'Ув. ' . htmlspecialchars($_POST['partnerInvite']['name']) . '. ';
    }
    if (!filter_var($_POST['partnerInvite']['email'], FILTER_VALIDATE_EMAIL)) {
        Tools::redirect(null, 'Вы ввели не корректный E-mail', 'danger');
    }
    $title = \Users\User::$cur->name() . ' приглашает вас зарегистрироваться на сайте: ' . idn_to_utf8(INJI_DOMAIN_NAME);
    $inviteCode = Tools::randomString(60);
    $invite = new Users\User\Invite();
    $invite->code = $inviteCode;
    $invite->type = 'UsersPartnerInvite';
    $invite->user_id = \Users\User::$cur->id;
    $invite->limit = 1;
    $invite->save();
    $text = "<h3>{$title}</h3>";
    $text .= '<p>Чтобы принять приглашение, перейдите по <a href = "http://' . idn_to_utf8(INJI_DOMAIN_NAME) . '/users/registration?invite_code=' . $inviteCode . '">этой ссылке</a> и завершите процедуру регистрации</p>';
    Tools::sendMail('noreply@' . INJI_DOMAIN_NAME, $_POST['partnerInvite']['email'], $title, $text);
    Tools::redirect(null, 'Приглашение было отправлено', 'success');
}
$partners = App::$cur->users->getUserPartners(Users\User::$cur, 8);
$usersSearch = [];
foreach ($partners['users'] as $user) {
    $usersSearch[] = [
        'name' => $user->name(),
        'id' => $user->pk(),
        'translit' => Tools::translit($user->name()),
    ];
}
if (!empty($_GET['info']['user_id']) && !empty($users[$_GET['info']['user_id']])) {
    
}
App::$cur->libs->loadLib('typeahead');
?>

<div class ='row'>
    <div class ='col-md-6'>
        <div class="users-cabinet-userTree">
            <h3>Ваши партнеры</h3>
            <h4>Партнеров в 8 поколениях: <b><?= $partners['count']; ?></b></h4>
            <div class ='form-group'>
                <input autocomplete="off" id ='partnersCabinetSearch' type="text" class ='form-control' placeholder="Поиск" />
            </div>
            <?php \Ui\Tree::ul(Users\User::$cur, 8); ?>
        </div>
        <script>
            inji.onLoad(function () {
              $('.users-cabinet-userTree li a').popover({
                trigger: 'click',
                placement: 'top',
                html: true,
                container: 'body',
                title: function () {
                  return '<span class="text-info"><strong>' + $(this).text() + '</strong></span>' +
                          '<button type="button" class="close" onclick="$(\'#' + $(this).parents('li').attr('id') + '\').find(\'>a\').popover(\'hide\');">&times;</button>'
                },
                content: function () {
                  var html = '';
                  var id = $(this).parents('li').attr('id').replace('Users_User-', '');
                  inji.Server.request({
                    url: '/Users/getPartnerInfo/' + id,
                    async: false,
                    success: function (result) {
                      html = result;
                    }
                  });
                  return html;
                }
              });
              $('.users-cabinet-userTree li a').click(function () {
                return false;
              })
            });
        </script>
    </div>
    <div class="col-md-6">
        <h3>Пригласить партнера</h3>
        <?php
        $form = new \Ui\Form();
        $form->begin();
        $form->input('text', 'partnerInvite[name]', 'Имя');
        $form->input('text', 'partnerInvite[email]', 'E-mail', ['required' => true]);
        $form->end('Пригласить');
        ?>
        <h3>Постоянные ссылки</h3>
        <?php
        $links = App::$cur->users->getSnippets('partnerLink');
        foreach ($links as $link) {
            echo "{$link['name']}:<pre>{$link['href']}</pre><br/>";
        }
        ?>
    </div>
</div>
<?php
if ($usersSearch) {
    ?>
    <script>
        inji.onLoad(function () {
          var $input2 = $('#partnersCabinetSearch');
          $input2.typeahead({
            source: <?= json_encode($usersSearch); ?>,
            autoSelect: true,
            matcher: function (item) {
              if (item.name.toLowerCase().indexOf(this.query) !== -1 || item.translit.toLowerCase().indexOf(this.query) !== -1) {
                return true
              }
            },
            updater: function (item) {
              return item.id;
            },
            displayText: function (item) {
              return item.name;
            },
            afterSelect: function (userid) {
              $input2.val('');
              $('.userList a').css('fontWeight', 400)
              $('.userList ul[style="display: block;"]').toggle();
              $('#Users_User-' + userid + '> a').css('fontWeight', 'bold');
              parent = $('#Users_User-' + userid).parent('ul');
              while (parent.length > 0) {
                if (parent.prev().prev().hasClass('glyphicon-chevron-right')) {
                  parent.prev().prev().click();
                }
                parent = $(parent).parent().parent('ul');
              }
              setTimeout(function () {
                $(window).scrollTop($('#Users_User-' + userid + ' a').offset().top - 100);
              }, 200)
            }

          });
        });


    </script>
    <?php
}
