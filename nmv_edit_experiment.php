<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('edit');

$form = new Form ('nmv_edit_experiment');

$form
	->setLabel('Biomedical Research');

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_experiment');
$form->addField ('experiment_title',TEXT,255,REQUIRED)  
    ->setLabel ('Title');
$form->addField ('classification',SELECT)
    ->setLabel ('Classification')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromTable ( 'nmv__experiment_classification', 'ID_exp_classification', 'english');
$form->addField ('ID_institution',SELECT)
    ->setLabel ('Institution')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromTable ( 'nmv__institution', 'ID_institution', "LEFT(concat(IFNULL(LEFT(institution_name, 60), '#'),' - ',IFNULL(LEFT(location,40), '#'),' - ',IFNULL(country, '#')),100)");
$form->addField ('location_details',TEXT,250)
    ->setLabel ('Location Details');
$form->addField ('start_year',TEXT,4)
    ->setLabel ('Start YMD')
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,2050);
$form->addField ('start_month',TEXT,2)
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,12)
    ->appendTo ('start_year');
$form->addField ('start_day',TEXT,2)
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,31)
    ->appendTo ('start_year');
$form->addField ('end_year',TEXT,4)
    ->setLabel ('End YMD')
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,2050);
$form->addField ('end_month',TEXT,2)
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,12)
    ->appendTo ('end_year');
$form->addField ('end_day',TEXT,2)
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,31)
    ->appendTo ('end_year');
$form->addField ('notes_location',TEXTAREA)
    ->setLabel ('Notes (Location)');
$form->addField ('funding',TEXT,250)
    ->setLabel ('Funding');
$form->addField ('field_of_interest',TEXT,50)
    ->setLabel ('Field of Interest');
$form->addField ('objective',TEXT,50)
    ->setLabel ('Objective');
$form->addField ('number_victims_estimate',TEXT,6) 
    ->setLabel ('Estimated Number of Victims')
    ->addCondition(VALUE,MIN,-1)
    ->addCondition(VALUE,MAX,1000000);
$form->addField ('number_fatalities_estimate',TEXT,6) 
    ->setLabel ('Estimated Number of Fatalities')
    ->addCondition(VALUE,MIN,-1)
    ->addCondition(VALUE,MAX,1000000);
$form->addField ('number_victims_remark',TEXTAREA)
    ->setLabel ('Remark about Number of Victims');
$form->addField ('confirmed_experiment',CHECKBOX,1)
    ->setLabel ('Confirmed Biomedical Research');
$form->addField ('notes',TEXTAREA)
    ->setLabel ('Notes');

$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__experiment')
	->addAction (REDIRECT,'nmv_view_experiment?ID_experiment={ID_experiment}');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Biomedical Research','nmv_list_experiments');

$layout
	->set('title',getUrlParameter('ID_experiment') ? 'Edit Biomedical Research' : 'New Biomedical Research')
	->set('content',$form->run().$form->debuglog->Show())
	->cast();
