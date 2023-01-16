<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('admin');

$form = new Form ('nmv_edit_experiment_classification');

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_exp_classification');
$form->addfield ('ID_exp_classification', PROTECTED_TEXT)
    ->setLabel ('classification ID');
$form->addField ('english',TEXT,250)
    ->setLabel ('english');


$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__experiment_classification')
	->addAction (REDIRECT,'nmv_list_experiment_classification');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Experiment Classification','nmv_list_experiment_classification');

$layout
	->set('title',getUrlParameter('ID_exp_classification') ? 'Edit experiment classification' : 'New experiment classification')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
