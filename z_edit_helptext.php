<?php
// CMS file: textblock management
// last known update: 2014-01-27

require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('system');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin.php');
$dbi->addBreadcrumb (L_HELPTEXTS,'z_list_helptexts.php');

$form = new Form ('edit_textblock');

$form
	->setLabel(NO_LABEL);

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('textblock_id');

$form->addField ('name',TEXT,30,REQUIRED)				->setLabel (L_HELPTEXT_NAME);
$form->addField ('permission',TEXT,15,REQUIRED)			->setLabel (L_HELPTEXT_PERMISSION);
$form->addField ('title_'.USER_LANGUAGE,TEXT,120)		->setLabel (L_HELPTEXT_TITLE);
$form->addField ('content_'.USER_LANGUAGE,TEXTAREA,20)	->setLabel (L_HELPTEXT_CONTENT);

if (getUrlParameter('textblock_id')) {
	$form->addField ('modified_user_id',HIDDEN,$dbi->getUserVar('user_id'));
	$form->addField ('modified_date',HIDDEN,date('Y-m-d'));
}
else {
	$form->addField ('created_user_id',HIDDEN,$dbi->getUserVar('user_id'));
	$form->addField ('created_date',HIDDEN,date('Y-m-d'));
}

$form
	->addButton (BACK)
	->addButton (APPLY)
	->addButton (SUBMIT);

$form
	->addAction (DATABASE,'z_textblocks')
	->addAction (REDIRECT,'z_list_helptexts.php');

$layout
	->set('title',getUrlParameter('textblock_id') ? L_EDIT_HELPTEXT : L_NEW_HELPTEXT)
	->set('content',$form->run ())
	->set('sidebar',$dbi->getTextblock_HTML ('simple_markup'))
	->cast();

?>