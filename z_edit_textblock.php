<?php
// CMS file: textblock management
// last known update: 2013-01-22

require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents.php');
$dbi->addBreadcrumb (L_TEXTBLOCKS,'z_list_textblocks.php');

$form = new Form ('edit_textblock');

$form
	->setLabel(NO_LABEL);

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('textblock_id');

$form->addField ('name',TEXT,30,REQUIRED)				->setLabel (L_TEXTBLOCK_NAME);
$form->addField ('title_'.USER_LANGUAGE,TEXT,120)		->setLabel (L_TEXTBLOCK_TITLE);
$form->addField ('content_'.USER_LANGUAGE,TEXTAREA,20)	->setLabel (L_TEXTBLOCK_CONTENT);

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
	->addAction (REDIRECT,'z_list_textblocks.php');

$layout
	->set('title',getUrlParameter('textblock_id') ? L_EDIT_TEXTBLOCK : L_NEW_TEXTBLOCK)
	->set('content',$form->run ())
	->set('sidebar',$dbi->getTextblock_HTML ('simple_markup'))
	->cast();

?>