<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('admin');

$form = new Form ('nmv_edit_diagnosis');


$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_diagnosis');
$form->addfield ('ID_diagnosis', PROTECTED_TEXT)
    ->setLabel ('diagnosis ID');
$form->addField ('english',TEXT,250)
    ->setLabel ('english');
$form->addField ('deutsch',TEXT,250)
    ->setLabel ('deutsch');
$form->addField ('type',SELECT)
    ->setLabel ('type')
		->addOption(NO_VALUE, 'please choose')
		->addOptionsFromArray(['clinical' => 'clinical', 'postmortem' => 'postmortem', 'sterilisation' => 'sterilisation']);


$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__diagnosis')
	->addAction (REDIRECT,'nmv_list_diagnosis');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Diagnosis','nmv_list_diagnosis');

$layout
	->set('title',getUrlParameter('ID_diagnosis') ? 'Edit diagnosis' : 'New diagnosis')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
