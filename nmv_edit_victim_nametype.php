<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('admin');

$form = new Form ('nmv_edit_victim_nametype');

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_nametype');
$form->addfield ('ID_nametype', PROTECTED_TEXT)
    ->setLabel ('nametype ID');
$form->addField ('english',TEXT,250)
    ->setLabel ('english');
$form->addField ('deutsch',TEXT,250)
    ->setLabel ('deutsch');


$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__victim_nametype')
	->addAction (REDIRECT,'nmv_list_victim_nametype');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Nametype','nmv_list_victim_nametype');

$layout
	->set('title',getUrlParameter('ID_nametype') ? 'Edit nametype' : 'New nametype')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
