<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('edit');

$form = new Form ('nmv_edit_victim');

$form
	->setLabel('Victim');

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_victim');
$form->addField ('surname',TEXT,255,REQUIRED)
    ->setClass ('keyboardInput')
    ->setLabel ('surname');
$form->addField ('first_names',TEXT,50)
    ->setClass ('keyboardInput')
    ->setLabel ('first names');
$form->addField ('ID_name',SUBTABLE,'nmv__victim_name',['victim_name', 'victim_first_names'])
    ->setLabel ('other names');
$form->addField ('birth_year',TEXT,4)
    ->setLabel ('birth ymd')
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,2950);
$form->addField ('birth_month',TEXT,2)
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,12)
    ->appendTo('birth_year');
$form->addField ('birth_day',TEXT,2)
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,31)
    ->appendTo('birth_year');
$form->addField ('twin',CHECKBOX,-1)
    ->setLabel ('twin')
    ->appendTo('birth_year');
$form->addField ('birth_place',TEXT,50)
    ->setClass ('keyboardInput')
    ->setLabel ('birth place');
$form->addField ('birth_country',TEXT,50)
    ->setClass ('keyboardInput')
    ->setLabel ('birth country');
$form->addField ('death_year',TEXT,4)
    ->setLabel ('death ymd')
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,2050);
$form->addField ('death_month',TEXT,2)
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,12)
    ->appendTo ('death_year');
$form->addField ('death_day',TEXT,2)
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,31)
    ->appendTo ('death_year');
$form->addField ('death_place',TEXT,50)
    ->setClass ('keyboardInput')
    ->setLabel ('death place');
$form->addField ('death_country',TEXT,50)
    ->setClass ('keyboardInput')
    ->setLabel ('death country');
$form->addField ('cause of death',TEXT,255)
    ->setLabel ('cause of death');
$form->addField ('gender',SELECT)
    ->setLabel ('gender')
    ->addOption (NO_VALUE,'please choose')
    ->addOption ('female')
    ->addOption ('male');
$form->addField ('ID_marital_family_status',SELECT)
    ->setLabel ('martial familiy status')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromTable ( 'nmv__marital_family_status', 'ID_marital_family_status', 'marital_family_status');
$form->addField ('ID_education',SELECT)
    ->setLabel ('highest education level')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromTable ( 'nmv__education', 'ID_education', 'education');
$form->addField ('religion',SELECT)
    ->setLabel ('religion')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromTable ( 'nmv__religion', 'ID_religion', 'religion');
$form->addField ('nationality_1938',SELECT)
    ->setLabel ('nationality (1938)')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromTable ( 'nmv__nationality', 'ID_nationality', 'nationality');
$form->addField ('ethnic_group',SELECT)
    ->setLabel ('ethnic group')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromTable ( 'nmv__ethnic_group', 'ID_ethnic_group', 'ethnic_group');
$form->addField ('occupation',SELECT)
    ->setLabel ('occupation')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromTable ( 'nmv__occupation', 'ID_occupation', 'occupation');
$form->addField ('occupation_details',TEXT,50)
    ->setLabel ('occupation_details');
$form->addField ('notes',TEXTAREA)
    ->setLabel ('notes');

$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__victim')
	->addAction (REDIRECT,'nmv_list_victims');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Victims','nmv_list_victims');

$layout
	->set('title',getUrlParameter('ID_victim') ? 'Edit Victim' : 'New Victim')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
