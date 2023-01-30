<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('admin');

$form = new Form ('nmv_edit_tissue_state');

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_tissue_state');
$form->addfield ('ID_tissue_state', PROTECTED_TEXT)
    ->setLabel ('ID tissue state');
$form->addField ('tissue_state',TEXT,250)
    ->setLabel ('tissue state');

$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__tissue_state')
	->addAction (REDIRECT,'nmv_list_tissue_state');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Tissue State','nmv_list_tissue_state');

$layout
	->set('title',getUrlParameter('ID_tissue_state') ? 'Edit tissue state' : 'New tissue state')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
