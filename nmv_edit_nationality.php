<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('admin');

$form = new Form ('nmv_edit_nationality');

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_nationality');
$form->addfield ('ID_nationality', PROTECTED_TEXT)
    ->setLabel ('nationality ID');
$form->addField ('nationality',TEXT,250)
    ->setLabel ('nationality');


$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__nationality')
	->addAction (REDIRECT,'nmv_list_nationality');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Nationality','nmv_list_nationality');

$layout
	->set('title',getUrlParameter('ID_nationality') ? 'Edit nationality' : 'New nationality')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
