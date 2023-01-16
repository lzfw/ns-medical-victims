<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('admin');

$form = new Form ('nmv_edit_tissue_form');

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_tissue_form');
$form->addfield ('ID_tissue_form', PROTECTED_TEXT)
    ->setLabel ('tissue form ID');
$form->addField ('english',TEXT,250)
    ->setLabel ('english');


$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__tissue_form')
	->addAction (REDIRECT,'nmv_list_tissue_form');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Tissue Form','nmv_list_tissue_form');

$layout
	->set('title',getUrlParameter('ID_tissue_form') ? 'Edit tissue form' : 'New tissue form')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
