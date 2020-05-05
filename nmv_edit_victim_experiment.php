<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('edit');

$form = new Form ('nmv_victim_experiment');

$vict_exp_id = (int) getUrlParameter('ID_vict_exp', 0);
$victim_id = (int) getUrlParameter('ID_victim', 0);


// query: get victim data
$victim_name = 'Error: Unknown.';
if ($victim_id) {
    $querystring = "
    SELECT CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_names, '')) victim_name
    FROM nmv__victim
    WHERE ID_victim = $victim_id";
    $query = $dbi->connection->query($querystring);
    $victim = $query->fetch_object();
    $victim_name = $victim->victim_name;
} else {
    $querystring = "
    SELECT CONCAT(COALESCE(v.surname, ''), ' ', COALESCE(v.first_names, '')) victim_name,
        v.ID_victim victim_id
    FROM nmv__victim v
    RIGHT JOIN nmv__victim_experiment h ON (h.ID_victim = v.ID_victim)
    WHERE ID_vict_exp = $vict_exp_id";
    $query = $dbi->connection->query($querystring);
    $victim = $query->fetch_object();
    $victim_id = $victim->victim_id;
    $victim_name = $victim->victim_name;
}

//query: get experiment-institutions for experiment SELECT
$querystring_experiment = "  SELECT e.ID_experiment AS value,
                                    CONCAT(IFNULL(e.experiment_title, 'no entry'), ' &ensp; - &ensp; ID ',
                                                  e.ID_experiment, ' &ensp; - &ensp; ',
                                                  IFNULL(i.institution_name, 'no entry')) AS title
                              FROM nmv__experiment e
                              LEFT JOIN nmv__institution i
                              ON e.ID_institution = i.ID_institution
                              ORDER BY title";



$form
	->setLabel('Biomedical Research: ' . $victim_name);

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_vict_exp');


$form->addField ('ID_victim',PROTECTED_TEXT)
    ->setLabel ('Victim ID');
$form->addField ('ID_experiment',SELECT)
    ->setLabel ('Biomedical Research')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromQuery ( "$querystring_experiment");
$form->addField ('experiment_duration',TEXT,50)
    ->setLabel ('Biomedical Research Duration');
$form->addField ('age_experiment_start',TEXT,3)
    ->setLabel ('Age (when Started)')
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,200);
$form->addField ('outcome_injuries',TEXTAREA)
    ->setLabel ('Injuries');
$form->addField ('ID_survival',SELECT)
    ->setLabel ('Survival')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromTable ( 'nmv__survival', 'ID_survival', 'english');
$form->addField ('not_corroborated',CHECKBOX,-1)
    ->setLabel ('Not Corroborated');
$form->addField ('exp_start_year',TEXT,4)
    ->setLabel ('Start Date YMD')
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,2950);
$form->addField ('exp_start_month',TEXT,2)
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,12)
    ->appendTo('exp_start_year');
$form->addField ('exp_start_day',TEXT,2)
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,31)
    ->appendTo('exp_start_year');
$form->addField ('exp_end_year',TEXT,4)
    ->setLabel ('End Date YMD')
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,2950);
$form->addField ('exp_end_month',TEXT,2)
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,12)
    ->appendTo('exp_end_year');
$form->addField ('exp_end_day',TEXT,2)
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,31)
    ->appendTo('exp_end_year');
$form->addField ('notes_perpetrator',TEXTAREA)
    ->setClass ('keyboardInput')
    ->setLabel ('Notes about Perpetrator');
$form->addField ('narratives',TEXTAREA)
    ->setClass ('keyboardInput')
    ->setLabel ('Narratives');
$form->addField ('notes',TEXTAREA)
    ->setClass ('keyboardInput')
    ->setLabel ('Notes');

$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__victim_experiment')
	->addAction (REDIRECT,'nmv_list_victim_experiment?ID_victim='.$victim_id);

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Victims','nmv_list_victims');
$dbi->addBreadcrumb ('Biomedical Research: '.$victim_name,'nmv_list_victim_experiment?ID_victim='.$victim_id);

$layout
	->set('title',getUrlParameter('ID_vict_exp') ? 'Edit Victim Biomedical Research Entry' : 'New Victim Biomedical Research Entry')
	->set('content',$form->run().$form->debuglog->Show())
	->cast();
