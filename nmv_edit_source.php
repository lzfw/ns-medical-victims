<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('edit');

$form = new Form ('nmv_edit_source');

$form
	->setLabel('Source');

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_source');
$form->addField ('ID_source', PROTECTED_TEXT)
    ->setLabel ('Source ID');
$form->addField ('source_title',TEXT,1500,REQUIRED) 
    ->setClass ('keyboardInput') 
    ->setLabel ('Title');
$form->addField ('signature',TEXT,50)
    ->setClass ('keyboardInput')
    ->setLabel ('Signature');
$form->addField ('creation_year',TEXT,50)
    ->setLabel ('Creation Year');
$form->addField ('pages',TEXT,50)
    ->setLabel ('Pages');
$form->addField ('type',TEXT,50)
    ->setLabel ('Type');
$form->addField ('ID_language',SELECT)
    ->setLabel ('Language')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromTable ( 'nmv__language', 'ID_language', 'english');
$form->addField ('description',TEXTAREA)
    ->setClass ('keyboardInput')
    ->setLabel ('Description');
$form->addField ('medium',TEXT,50)
    ->setLabel ('Medium');
/* Aleks requested so, 2019-05-02
$form->addField ('person_in_charge',TEXT,50)
    ->setLabel ('Person in charge');
*/
$form->addField ('published_source',CHECKBOX,-1)
    ->setLabel ('Published source');
/* Aleks requested so, 2019-05-02
$form->addField ('names_mentioned',TEXT,6)
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,1000000)
    ->setLabel ('Names mentioned (count)');
*/
$form->addField ('location',TEXT,250)
    ->setClass ('keyboardInput')
    ->setLabel ('Location');
$form->addField ('notes',TEXTAREA)
    ->setClass ('keyboardInput')
    ->setLabel ('Notes');

$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__source')
	->addAction (REDIRECT,'nmv_view_source?ID_source={ID_source}');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Sources','nmv_list_sources');

$layout
	->set('title',getUrlParameter('ID_source') ? 'Edit Source' : 'New Source')
	->set('content',$form->run().$form->debuglog->Show())
	->cast();
