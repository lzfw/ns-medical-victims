<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('admin');

$form = new Form ('nmv_edit_language');

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_language');
$form->addfield ('ID_language', PROTECTED_TEXT)
    ->setLabel ('language ID');
$form->addField ('english',TEXT,250)
    ->setLabel ('english');
$form->addField ('deutsch',TEXT,250)
    ->setLabel ('deutsch');


$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__language')
	->addAction (REDIRECT,'nmv_list_language');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Language','nmv_list_language');

$layout
	->set('title',getUrlParameter('ID_language') ? 'Edit language' : 'New language')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
