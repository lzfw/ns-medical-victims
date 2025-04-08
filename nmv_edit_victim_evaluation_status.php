<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('admin');

$form = new Form ('nmv_edit_victim_evaluation_status');

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_evaluation_status');
$form->addfield ('ID_evaluation_status', PROTECTED_TEXT)
    ->setLabel ('ID status');
$form->addField ('status',TEXT,250)
    ->setLabel ('status');


$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__victim_evaluation_status')
	->addAction (REDIRECT,'nmv_list_victim_evaluation_status');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Status','nmv_list_victim_evaluation_status');

$layout
	->set('title',getUrlParameter('ID_evaluation_status') ? 'Edit status' : 'New status')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
