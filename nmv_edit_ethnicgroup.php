<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('admin');

$form = new Form ('nmv_edit_ethnicgroup');

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_ethnicgroup');
$form->addfield ('ID_ethnicgroup', PROTECTED_TEXT)
    ->setLabel ('ethnicgroup ID');
$form->addField ('english',TEXT,250)
    ->setLabel ('english');
$form->addField ('deutsch',TEXT,250)
    ->setLabel ('deutsch');


$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__ethnicgroup')
	->addAction (REDIRECT,'nmv_list_ethnicgroup');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Ethnicgroup','nmv_list_ethnicgroup');

$layout
	->set('title',getUrlParameter('ID_ethnicgroup') ? 'Edit ethnicgroup' : 'New ethnicgroup')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
