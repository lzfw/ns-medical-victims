<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('admin');

$form = new Form ('nmv_edit_victim_evaluation_compensation');

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_compensation');
$form->addfield ('ID_compensation', PROTECTED_TEXT)
    ->setLabel ('compensation ID');
$form->addField ('english',TEXT,250)
    ->setLabel ('english');
$form->addField ('deutsch',TEXT,250)
    ->setLabel ('deutsch');


$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__victim_evaluation_compensation')
	->addAction (REDIRECT,'nmv_list_victim_evaluation_compensation');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Compensation','nmv_list_victim_evaluation_compensation');

$layout
	->set('title',getUrlParameter('ID_compensation') ? 'Edit compensation' : 'New compensation')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
