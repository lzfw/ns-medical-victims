<?php
// CMS file: remote management
// last known update: 2014-01-27

require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('system');

$form = new Form ('edit_remote_access');

$form
	->setLabel(NO_LABEL);

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('user_id');

$form->addField ('display_name',TEXT,60,REQUIRED)	->setLabel (L_USER_DISPLAY_NAME);
$form->addField ('order_name',TEXT,60,REQUIRED)		->setLabel (L_USER_ORDER_NAME);
$form->addField ('name',TEXT,15,REQUIRED)			->setLabel (L_USER_NAME);
$form->addField ('remote',TEXT,30,REQUIRED)			->setLabel (L_IP_ADDRESS);

$form->addField ('group',TEXT,15,REQUIRED)			->setLabel (L_USER_GROUP);
$form
	->addField ('permissions',SELECT,OPTIONAL)
	->setLabel (L_USER_PERMISSIONS)
		->addOption (NULL,'none')
		->addOption ('view')
		->addOption ('view, edit');

$form
	->addButton (BACK)
	->addButton (APPLY)
	->addButton (SUBMIT);

$form
	->addAction (DATABASE,'z_users')
	->addAction (REDIRECT,'z_list_remote');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_REMOTE_ACCESS,'z_list_remote');

$layout
	->set('title',getUrlParameter('user_id') ? L_EDIT_REMOTE_ACCESS : L_NEW_REMOTE_ACCESS)
	->set('content',$form->run ())
	->cast();

?>