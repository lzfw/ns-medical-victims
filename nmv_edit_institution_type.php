<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('admin');

$form = new Form ('nmv_edit_institution_type');

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_institution_type');
$form->addfield ('ID_institution_type', PROTECTED_TEXT)
    ->setLabel ('institution type ID');
$form->addField ('english',TEXT,250)
    ->setLabel ('english');


$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__institution_type')
	->addAction (REDIRECT,'nmv_list_institution_type');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Institution Type','nmv_list_institution_type');

$layout
	->set('title',getUrlParameter('ID_institution_type') ? 'Edit institution type' : 'New institution type')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
