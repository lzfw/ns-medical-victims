<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('admin');

$form = new Form ('nmv_edit_country');

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_country');
$form->addfield ('ID_country', PROTECTED_TEXT)
    ->setLabel ('country ID');
$form->addField ('english',TEXT,250)
    ->setLabel ('english');
$form->addField ('local_name',TEXT,250)
    ->setLabel ('local name');


$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__country')
	->addAction (REDIRECT,'nmv_list_country');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Country','nmv_list_country');

$layout
	->set('title',getUrlParameter('ID_country') ? 'Edit country' : 'New country')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
