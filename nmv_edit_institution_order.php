<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('admin');

$form = new Form ('nmv_edit_institution_order');

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_institution_order');
$form->addfield ('ID_institution_order', PROTECTED_TEXT)
    ->setLabel ('institution order ID');
$form->addField ('english',TEXT,250)
    ->setLabel ('english');


$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__institution_order')
	->addAction (REDIRECT,'nmv_list_institution_order');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Institution Order','nmv_list_institution_order');

$layout
	->set('title',getUrlParameter('ID_institution_order') ? 'Edit institution order' : 'New institution order')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
