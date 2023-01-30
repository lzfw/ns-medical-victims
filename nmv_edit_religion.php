<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('admin');

$form = new Form ('nmv_edit_religion');

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_religion');
$form->addfield ('ID_religion', PROTECTED_TEXT)
    ->setLabel ('ID religion');
$form->addField ('religion',TEXT,250)
    ->setLabel ('religion');


$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__religion')
	->addAction (REDIRECT,'nmv_list_religion');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Religion','nmv_list_religion');

$layout
	->set('title',getUrlParameter('ID_religion') ? 'Edit religion' : 'New religion')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
