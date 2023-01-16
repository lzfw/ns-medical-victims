<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('admin');

$form = new Form ('nmv_edit_educational_abilities');

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_educational_abilities');
$form->addfield ('ID_educational_abilities', PROTECTED_TEXT)
    ->setLabel ('educational abilities ID');
$form->addField ('english',TEXT,250)
    ->setLabel ('english');

$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__educational_abilities')
	->addAction (REDIRECT,'nmv_list_educational_abilities');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Educational Abilities','nmv_list_educational_abilities');

$layout
	->set('title',getUrlParameter('ID_educational_abilities') ? 'Edit educational abilities' : 'New educational abilities')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
