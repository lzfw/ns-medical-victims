<?php
// CMS file: user management
// last known update: 2013-01-22

require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('admin');

$uid = getUrlParameter('user_id');

$form = new Form ('z_edit_user');

$form
	->setLabel(NO_LABEL);

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('user_id');

$form->addField ('display_name',TEXT,60,REQUIRED)	->setLabel (L_USER_DISPLAY_NAME);
$form->addField ('order_name',TEXT,60,REQUIRED)		->setLabel (L_USER_ORDER_NAME);
$form->addField ('name',TEXT,15,REQUIRED)			->setLabel (L_USER_NAME);
if ($uid) {
	$form->addField ('password_text',PASSWORD,15)	->setLabel (L_USER_PASSWORD);
} else {
	$form->addField ('password_text',PASSWORD,15,REQUIRED)	->setLabel (L_USER_PASSWORD);
}

//OBACHT: all am Ende des Projektes obsolet
if ($dbi->checkUserPermission('system')) {
	$form->addField ('group',TEXT,15,REQUIRED)
		->setLabel (L_USER_GROUP);
	$form
		->addField ('permissions',SELECT,OPTIONAL)
		->setLabel (L_USER_PERMISSIONS)
			->addOption (NULL,'none')
			->addOption ('view')
			->addOption ('view, mpg')
			->addOption ('view, edit')
			->addOption ('view, edit, all')
			->addOption ('view, edit, admin')
			->addOption ('view, edit, admin, all')
			->addOption ('view, edit, admin, system')
			->addOption ('view, edit, admin, system, all')
			->addOption ('view, admin')
			->addOption ('view, admin, system')
			->addOption ('view, system');
}
elseif ($dbi->checkUserPermission('admin')) {
	$form->addField ('group',PROTECTED_TEXT,$dbi->getUserVar('group'))
		->setLabel (L_USER_GROUP);
	$form
		->addField ('permissions',SELECT,OPTIONAL)
		->setLabel (L_USER_PERMISSIONS)
			->addOption (NULL,'none')
			->addOption ('view')
			->addOption ('view, mpg')
			->addOption ('view, edit')
			->addOption ('view, edit, all')
			->addOption ('view, edit, admin')
			->addOption ('view, edit, admin, all')
			->addOption ('view, admin');
}

$form->addField ('profile_'.USER_LANGUAGE,TEXTAREA,6) ->setLabel (L_USER_PROFILE);
$form->addField ('profile_hide',CHECKBOX) ->setLabel (L_USER_PROFILE_HIDE);

$form->addCondition (USER_FUNCTION, function() use ($form, $uid) {
	$pass = $form->Fields['password_text']->user_value;
	if ($pass) {
		$form->addField ('password',PASSWORD,15);
		$form->Fields['password']->user_value = password_hash($pass, PASSWORD_DEFAULT);
	}
	if (!$uid && !$pass) {
		return false;
	}
	unset($form->Fields['password_text']);
	return true;
}, L_PASSWORD_UPDATE_FAILED);

$form
	->addButton (BACK)
	->addButton (APPLY)
	->addButton (SUBMIT);

$form
	->addAction (DATABASE,'z_users')
	->addAction (REDIRECT,'z_list_users');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');

$layout
	->set('title',$uid ? L_EDIT_USER_ACCOUNT : L_NEW_USER_ACCOUNT)
	->set('content',$form->run ())
	->cast();

?>
