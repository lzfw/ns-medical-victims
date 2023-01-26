<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('admin');

$form = new Form ('nmv_edit_ethnic_group');

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_ethnic_group');
$form->addfield ('ID_ethnic_group', PROTECTED_TEXT)
    ->setLabel ('ID Ethnic Group');
$form->addField ('english',TEXT,250)
    ->setLabel ('english');

$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__ethnic_group')
	->addAction (REDIRECT,'nmv_list_ethnic_group');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Ethnic Group','nmv_list_ethnic_group');

$layout
	->set('title',getUrlParameter('ID_ethnic_group') ? 'Edit ethnic_group' : 'New ethnic_group')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
