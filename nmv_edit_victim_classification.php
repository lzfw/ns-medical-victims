<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('admin');

$form = new Form ('nmv_edit_victim_classification');

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_classification');
$form->addfield ('ID_classification', PROTECTED_TEXT)
    ->setLabel ('classification ID');
$form->addField ('english',TEXT,250)
    ->setLabel ('english');
$form->addField ('deutsch',TEXT,250)
    ->setLabel ('deutsch');


$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__victim_classification')
	->addAction (REDIRECT,'nmv_list_victim_classification');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Victim Classification (Imprisonment)','nmv_list_victim_classification');

$layout
	->set('title',getUrlParameter('ID_classification') ? 'Edit classification' : 'New classification')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
