<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('admin');

$form = new Form ('nmv_edit_survival');

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_survival');
$form->addfield ('ID_survival', PROTECTED_TEXT)
    ->setLabel ('survival ID');
$form->addField ('english',TEXT,250)
    ->setLabel ('english');


$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__survival')
	->addAction (REDIRECT,'nmv_list_survival');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Survival','nmv_list_survival');

$layout
	->set('title',getUrlParameter('ID_survival') ? 'Edit survival' : 'New survival')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
