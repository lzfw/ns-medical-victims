<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('admin');

$form = new Form ('nmv_edit_marital_family_status');

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_marital_family_status');
$form->addfield ('ID_marital_family_status', PROTECTED_TEXT)
    ->setLabel ('status ID');
$form->addField ('english',TEXT,250)
    ->setLabel ('english');
$form->addField ('deutsch',TEXT,250)
    ->setLabel ('deutsch');


$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__marital_family_status')
	->addAction (REDIRECT,'nmv_list_marital_family_status');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Marital / Family Status','nmv_list_marital_family_status');

$layout
	->set('title',getUrlParameter('ID_marital_family_status') ? 'Edit marital / family mstatus' : 'New marital / family status')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
