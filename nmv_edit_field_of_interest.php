<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('admin');

$form = new Form ('nmv_edit_field_of_interest');

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_foi');
$form->addfield ('ID_foi', PROTECTED_TEXT)
    ->setLabel ('ID field of interest');
$form->addField ('field_of_interest',TEXT,250)
    ->setLabel ('field of interest');



$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__field_of_interest')
	->addAction (REDIRECT,'nmv_list_field_of_interest');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Field of Interest','nmv_list_field_of_interest');

$layout
	->set('title',getUrlParameter('ID_foi') ? 'Edit Field of Interest' : 'New Field of Interest')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
