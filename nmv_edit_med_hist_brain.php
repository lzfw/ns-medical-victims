<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('edit');

$form = new Form ('nmv_med_hist_brain');

// query: get victim data
$victim_id = (int) getUrlParameter('ID_victim', 0);
$victim_name = 'Error: Unknown.';
if ($victim_id) {
    $querystring = "
    SELECT CONCAT(surname, ' ', first_names) victim_name
    FROM nmv__victim
    WHERE ID_victim = $victim_id";
    $query = $dbi->connection->query($querystring);
    $victim = $query->fetch_object();
    $victim_name = $victim->victim_name;
} else {
    $med_id = (int) getUrlParameter('ID_med_history_brain', 0);
    $querystring = "
    SELECT CONCAT(v.surname, ' ', v.first_names) victim_name,
        v.ID_victim victim_id
    FROM nmv__victim v
    RIGHT JOIN nmv__med_history_brain h ON (h.ID_victim = v.ID_victim)
    WHERE ID_med_history_brain = $med_id";
    $query = $dbi->connection->query($querystring);
    $victim = $query->fetch_object();
    $victim_id = $victim->victim_id;
    $victim_name = $victim->victim_name;
}


$form
	->setLabel('Brain research related to victim ' . $victim_name);

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_med_history_brain');

$form->addField ('ID_victim',PROTECTED_TEXT)
    ->setLabel ('Victim ID');
$form->addField ('ID_institution',SELECT)
    ->setLabel ('Research Institution')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromTable ( 'nmv__institution', 'ID_institution', "LEFT(concat(IFNULL(LEFT(institution_name, 60), '#'),' - ',IFNULL(LEFT(location,40), '#'),' - ',IFNULL(country, '#')),100)");
$form->addField ('kwi_researcher',TEXT,150)
    ->setClass ('keyboardInput')
    ->setLabel ('KWI Researcher');
$form->addField ('ID_diagnosis',SELECT)
    ->setLabel ('Brain Diagnosis')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromTable ( 'nmv__diagnosis', 'ID_diagnosis', 'english');
$form->addField ('diagnosis',TEXT, 70)
    ->appendTo('ID_diagnosis');
$form->addField ('brain_report_year',TEXT,4) 
    ->setLabel ('Brain Report Date YMD')
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,2950);
$form->addField ('brain_report_month',TEXT,2)
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,12)
    ->appendTo('brain_report_year');
$form->addField ('brain_report_day',TEXT,2)
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,31)
    ->appendTo('brain_report_year');
$form->addField ('notes',TEXTAREA)
    ->setClass ('keyboardInput')
    ->setLabel ('Notes');
$form->addField ('ref_no',TEXT,50) 
    ->setLabel ('Reference number');

$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__med_history_brain')
	->addAction (REDIRECT,'nmv_list_med_hist?ID_victim='.$victim_id);

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Victims','nmv_list_victims');
$dbi->addBreadcrumb ('Medical History of '.$victim_name,'nmv_list_med_hist?ID_victim='.$victim_id);

$layout
	->set('title',getUrlParameter('ID_med_history_brain') ? 'Edit Brain Research' : 'New Brain Research')
	->set('content',$form->run().$form->debuglog->Show())
	->cast();
