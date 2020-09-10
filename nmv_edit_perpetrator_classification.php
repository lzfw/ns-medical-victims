<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('admin');

$form = new Form ('nmv_edit_perpetrator_classification');

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_perp_class');
$form->addfield ('ID_perp_class', PROTECTED_TEXT)
    ->setLabel ('classification ID');
$form->addField ('english',TEXT,250)
    ->setLabel ('english');
$form->addField ('deutsch',TEXT,250)
    ->setLabel ('deutsch');


$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__perpetrator_classification')
	->addAction (REDIRECT,'nmv_list_perpetrator_classification');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Perpetrator Classification','nmv_list_perpetrator_classification');

$layout
	->set('title',getUrlParameter('ID_perp_class') ? 'Edit perpetrator classification' : 'New perpetrator classification')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
