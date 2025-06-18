<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('admin');

$id = getUrlParameter('id');


$form = new Form ('website_user_edit');

$form
    ->setLabel('User');

$form
    ->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
    ->setPrimaryKeyName('id');
$form->addField ('id', PROTECTED_TEXT)
    ->setLabel ('ID');
$form->addField ('name',TEXT, 255, REQUIRED)
    ->setClass ('keyboardInput')
    ->setLabel ('User Name');
$form->addField ('email',TEXT, 255, REQUIRED)
    ->setClass ('keyboardInput')
    ->setLabel ('Email');
$form->addField ('password_text',TEXT, 255)
    ->setClass ('keyboardInput')
    ->setLabel ('Password');
$form->addField ('role',TEXT, 255)
    ->setClass ('keyboardInput')
    ->setLabel ('Role');
$form->addField('access_pending', RADIO, '', '1')
    ->setLabel('Access pending')
    ->addRadioButton(0, ' no')
    ->addRadioButton(1, ' yes');
$form->addField('access_granted', RADIO, '', '0')
    ->setLabel('Access granted')
    ->addRadioButton(0, ' no')
    ->addRadioButton(1, ' yes');
$form->addField('must_change_password', RADIO, '', '1')
    ->setLabel('Must change password')
    ->addRadioButton(0, ' no')
    ->addRadioButton(1, ' yes');
$form->addField ('access_expires_at',TEXT, 10)
    ->setClass ('keyboardInput')
    ->setLabel ('Access expires at (yyyy-mm-dd)');

$form->addCondition (USER_FUNCTION, function() use ($form, $id) {
    $pass = $form->Fields['password_text']->user_value;
    if ($pass) {
        $form->addField ('password',PASSWORD,15);
        $form->Fields['password']->user_value = password_hash($pass, PASSWORD_DEFAULT);
    }
    if (!$id && !$pass) {
        return false;
    }
    unset($form->Fields['password_text']);
    return true;
}, L_PASSWORD_UPDATE_FAILED);

$form
    ->addButton (SUBMIT)
    ->addButton (APPLY);

$form
    ->addAction (DATABASE,'users')
    ->addAction (REDIRECT,'website_user_list');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'website_user_list');



$layout
    ->set('title',getUrlParameter('id') ? 'Edit user account' : 'New user account')
    ->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
    ->cast();
