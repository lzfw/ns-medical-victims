<?php
// CMS file: user management
// last known update: 2013-01-22

require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('admin');

$form = new Form ('z_edit_user');

$form
	->setLabel(NO_LABEL);

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('user_id');

$form->addField ('display_name',TEXT,60,REQUIRED)	->setLabel (L_USER_DISPLAY_NAME);
$form->addField ('order_name',TEXT,60,REQUIRED)		->setLabel (L_USER_ORDER_NAME);
$form->addField ('name',TEXT,15,REQUIRED)			->setLabel (L_USER_NAME);
$form->addField ('password',PASSWORD,15,REQUIRED)	->setLabel (L_USER_PASSWORD);

if ($dbi->checkUserPermission('system')) {
	$form->addField ('group',TEXT,15,REQUIRED)
		->setLabel (L_USER_GROUP);
	$form
		->addField ('permissions',SELECT,OPTIONAL)
		->setLabel (L_USER_PERMISSIONS)
			->addOption (NULL,'none')
			->addOption ('view')
			->addOption ('view, edit')
			->addOption ('view, edit, admin')
			->addOption ('view, edit, admin, system')
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
			->addOption ('view, edit')
			->addOption ('view, edit, admin')
			->addOption ('view, admin');
}

$form->addField ('profile_'.USER_LANGUAGE,TEXTAREA,6) ->setLabel (L_USER_PROFILE);
$form->addField ('profile_hide',CHECKBOX) ->setLabel (L_USER_PROFILE_HIDE);

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
	->set('title',getUrlParameter('user_id') ? L_EDIT_USER_ACCOUNT : L_NEW_USER_ACCOUNT)
	->set('content',$form->run ())
	->cast();

?>