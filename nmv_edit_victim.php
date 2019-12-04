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
$form->addField ('ID_victim',PROTECTED_TEXT)
    ->setLabel ('Victim ID');	
$form->addField ('surname',TEXT,255,REQUIRED)  
    ->setLabel ('Surname');
$form->addField ('first_names',TEXT,50)
    ->setLabel ('First Names');
$form->addField ('mpg_project',CHECKBOX,-1)
    ->setLabel ('MPG project');
$form->addField ('ID_dataset_origin',SELECT)
    ->setLabel ('Origin Dataset')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromTable ( 'nmv__dataset_origin', 'ID_dataset_origin', 'work_group');
$form->addField ('birth_year',TEXT,4) 
    ->setLabel ('Birth YYYYMD')
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
    ->setLabel ('Twin')
    ->appendTo('birth_year');
$form->addField ('birth_place',TEXT,250)
    ->setLabel ('Birth Place');
$form->addField ('birth_country',TEXT,50)
    ->setLabel ('Birth Country');
$form->addField ('death_year',TEXT,4)
    ->setLabel ('Death YYYYMD')
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
$form->addField ('death_place',TEXT,250)
    ->setLabel ('Death Place');
$form->addField ('death_country',TEXT,50)
    ->setLabel ('Death Country');
$form->addField ('cause_of_death',TEXT,255)
    ->setLabel ('Cause of Death');
$form->addField ('gender',SELECT)
    ->setLabel ('Gender')
    ->addOption (NO_VALUE,'please choose')
    ->addOption ('female')
    ->addOption ('male');
$form->addField ('ID_marital_family_status',SELECT)
    ->setLabel ('Marital Familiy Status')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromTable ( 'nmv__marital_family_status', 'ID_marital_family_status', 'english');
$form->addField ('ID_education',SELECT)
    ->setLabel ('Highest Education Level')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromTable ( 'nmv__education', 'ID_education', 'english');
$form->addField ('religion',SELECT)
    ->setLabel ('Religion')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromTable ( 'nmv__religion', 'ID_religion', 'english');
$form->addField ('nationality_1938',SELECT)
    ->setLabel ('Nationality (1938)')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromTable ( 'nmv__nationality', 'ID_nationality', 'english');
$form->addField ('ethnic_group',SELECT)
    ->setLabel ('Ethnic Group')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromTable ( 'nmv__ethnicgroup', 'ID_ethnicgroup', 'english');
$form->addField ('occupation',SELECT)
    ->setLabel ('Occupation')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromTable ( 'nmv__occupation', 'ID_occupation', 'english');
$form->addField ('occupation_details',TEXT,50)
    ->setLabel ('Occupation Details');
$form->addField ('notes',TEXTAREA)
    ->setLabel ('Notes');

// Arrests group
$form->addField ('arrest_prehistory',TEXTAREA)
    ->setLabel ('Arrest prehistory');
$form->addField ('arrest_location',TEXT,255)
    ->setLabel ('Arrest location');
$form->addField ('arrest_country',TEXT,100)
    ->setLabel ('Arrest country');
$form->addField ('arrest_history',TEXTAREA)
    ->setLabel ('Arrest history');

// After '45 group
$form->addField ('residence_after_1945_place',TEXT,250)
    ->setLabel ('Residence after \'45 (Place)');
$form->addField ('residence_after_1945_country',TEXT,50)
    ->setLabel ('Residence after \'45 (Country)');
$form->addField ('occupation_after_1945',TEXT,50)
    ->setLabel ('Occupation after \'45');
$form->addField ('nationality_after_1945',SELECT)
    ->setLabel ('Nationality after 1945')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromTable ( 'nmv__nationality', 'ID_nationality', 'english');
$form->addField ('consequential_injuries',TEXTAREA)
    ->setLabel ('Consequential injuries');
$form->addField ('compensation',CHECKBOX,-1)
    ->setLabel ('Compensation ???');
$form->addField ('compensation_details',TEXTAREA)
    ->setLabel ('Compensation details');
$form->addField ('notes_after_1945',TEXTAREA)
    ->setLabel ('Notes on life after 1945');


$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__victim')
	->addAction (REDIRECT,'nmv_view_victim?ID_victim={ID_victim}');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Victims','nmv_list_victims');

$layout
	->set('title',getUrlParameter('ID_victim') ? 'Edit Victim' : 'New Victim')
	->set('content',$form->run().$form->debuglog->Show())
	->cast();
