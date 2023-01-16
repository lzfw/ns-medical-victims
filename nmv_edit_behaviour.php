<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('admin');

$form = new Form ('nmv_edit_behaviour');

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_behaviour');
$form->addfield ('ID_behaviour', PROTECTED_TEXT)
    ->setLabel ('behaviour ID');
$form->addField ('english',TEXT,250)
    ->setLabel ('english');


$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__behaviour')
	->addAction (REDIRECT,'nmv_list_behaviour');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Behaviour','nmv_list_behaviour');

$layout
	->set('title',getUrlParameter('ID_behaviour') ? 'Edit behaviour' : 'New behaviour')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
