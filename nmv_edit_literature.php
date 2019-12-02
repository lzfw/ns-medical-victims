<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('edit');

$form = new Form ('nmv_edit_literature');

$form
	->setLabel('Literature');

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_literature');
$form->addField ('ID_literature', PROTECTED_TEXT)
    ->setLabel ('Literature ID');
$form->addField ('authors',TEXT,250)  
    ->setLabel ('Authors');
$form->addField ('lit_year',TEXT,50)  
    ->setLabel ('Year');
$form->addField ('lit_title',TEXT,1500)  
    ->setLabel ('Title');

// article properties - should be grouped somehow
$form->addField ('article',CHECKBOX,-1)
    ->setLabel ('Is article');
$form->addField ('journal_or_series',TEXT,1500)  
    ->setLabel ('Journal or series');
$form->addField ('editor',TEXT,250)  
    ->setLabel ('Editor');
$form->addField ('volume',TEXT,50)  
    ->setLabel ('Volume');
$form->addField ('pages',TEXT,50)  
    ->setLabel ('Pages');

$form->addField ('location',TEXT,250)  
    ->setLabel ('Location');
$form->addField ('publisher',TEXT,250)  
    ->setLabel ('Publisher');
/* Aleks requested so on Skype: 2019-05-02
$form->addField ('data_entry_status',TEXT,255)
    ->setLabel ('data entry status');
$form->addField ('names_mentioned',TEXT,6)
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,1000000)
    ->setLabel ('names mentioned (count)');
    */
$form->addField ('scientific_exploitation',CHECKBOX,1)
    ->setLabel ('Scientific exploitation');
$form->addField ('notes',TEXTAREA)
    ->setLabel ('Notes');

$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__literature')
	->addAction (REDIRECT,'nmv_view_literature?ID_literature={ID_literature}');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Literature','nmv_list_literature');

$layout
	->set('title',getUrlParameter('ID_literature') ? 'Edit literature' : 'New literature')
	->set('content',$form->run().$form->debuglog->Show())
	->cast();
