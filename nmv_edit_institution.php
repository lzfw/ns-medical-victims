<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('edit');

$form = new Form ('nmv_edit_institution');

$form
	->setLabel('Institution');

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_institution');
$form->addfield ('ID_institution', PROTECTED_TEXT)
    ->setLabel ('Institution ID');
$form->addField ('institution_name',TEXT,250)  
    ->setLabel ('Name');
$form->addField ('location',TEXT,250)  
    ->setLabel ('Location');
$form->addField ('country',TEXT,50)  
    ->setLabel ('Country');
$form->addField ('type',SELECT)
    ->setLabel ('Type')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromTable ( 'nmv__institution_type', 'ID_institution_type', 'english');
$form->addField ('notes',TEXTAREA)
    ->setLabel ('Notes');

$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__institution')
	->addAction (REDIRECT,'nmv_view_institution?ID_institution={ID_institution}');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Institutions','nmv_list_institutions');

$layout
	->set('title',getUrlParameter('ID_institution') ? 'Edit institution' : 'New institution')
	->set('content',$form->run().$form->debuglog->Show())
	->cast();
