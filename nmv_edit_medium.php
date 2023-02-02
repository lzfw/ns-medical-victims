<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('admin');

$form = new Form ('nmv_edit_medium');

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_medium');
$form->addfield ('ID_medium', PROTECTED_TEXT)
    ->setLabel ('ID medium');
$form->addField ('medium',TEXT,250)
    ->setLabel ('medium');


$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__medium')
	->addAction (REDIRECT,'nmv_list_medium');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('medium','nmv_list_medium');

$layout
	->set('title',getUrlParameter('ID_medium') ? 'Edit medium' : 'New medium')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
