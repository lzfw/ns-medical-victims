<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('admin');

$form = new Form ('nmv_edit_dataset_origin');

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_dataset_origin');
$form->addfield ('ID_dataset_origin', PROTECTED_TEXT)
    ->setLabel ('dataset origin ID');
$form->addField ('work_group',TEXT,250)
    ->setLabel ('work group');



$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__dataset_origin')
	->addAction (REDIRECT,'nmv_list_dataset_origin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Dataset Origin','nmv_list_dataset_origin');

$layout
	->set('title',getUrlParameter('ID_dataset_origin') ? 'Edit dataset origin' : 'New dataset origin')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
