<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('edit');

$form = new Form ('nmv_prisoner_assistant_experiment');

$pa_exp_id = (int) getUrlParameter('ID_pa_exp', 0);
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
    RIGHT JOIN nmv__prisoner_assistant_experiment h ON (h.ID_victim = v.ID_victim)
    WHERE ID_pa_exp = $pa_exp_id";
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
	->setPrimaryKeyName('ID_pa_exp');


$form->addField('ID_victim',PROTECTED_TEXT)
    ->setLabel('ID Person');
$form->addField('ID_experiment',SELECT,REQUIRED)
    ->setLabel('Biomedical Research')
    ->addOption(NO_VALUE,'please choose')
    ->addOptionsFromQuery("$querystring_experiment");
$form->addField('exp_start_day',TEXT,2)
    ->setLabel('start date of involvement DMYYYY')
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,31);
$form->addField('exp_start_month',TEXT,2)
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,12)
    ->appendTo('exp_start_day');
$form->addField('exp_start_year',TEXT,4)
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,1950)
    ->appendTo('exp_start_day');
$form->addField('exp_end_day',TEXT,2)
    ->setLabel('end date of involvement DMYYYY')
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,31);
$form->addField('exp_end_month',TEXT,2)
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,12)
    ->appendTo('exp_end_day');
$form->addField('exp_end_year',TEXT,4)
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,2950)
    ->appendTo('exp_end_day');
$form->addField('experiment_duration',TEXT,50)
    ->setLabel('duration of involvement in experiment');
$form->addField('age_experiment_start',TEXT,3)
    ->setLabel('age (at start of involvement)')
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,200);
$form->addField('ID_role',SELECT)
    ->setLabel('role in experiment')
    ->addOption(NO_VALUE,'please choose')
    ->addOptionsFromTable('nmv__role', 'ID_role', 'role');
$form->addField('role_other',TEXT,50)
    ->setClass('keyboardInput')
    ->setLabel('other role in experiment <br> (if role not found in selection)');
$form->addField('gave_testimony_in_trial', RADIO, '', 'no information')
    ->setLabel('gave testimony in trial')
    ->addRadioButton('yes', ' yes')
    ->addRadioButton('no', ' no')
    ->addRadioButton('no information', ' no information');
$form->addField('narratives',TEXTAREA)
    ->setClass ('keyboardInput')
    ->setLabel ('subjective narratives');
$form->addField('notes_about_involvement',TEXTAREA)
    ->setClass ('keyboardInput')
    ->setLabel ('notes about involvement');


$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__prisoner_assistant_experiment')
	->addAction (REDIRECT,'nmv_list_victim_experiment?ID_victim='.$victim_id);

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Victims','nmv_list_victims');
$dbi->addBreadcrumb ('Biomedical Research: '.$victim_name,'nmv_list_victim_experiment?ID_victim='.$victim_id);

$layout
	->set('title',getUrlParameter('ID_pa_exp') ? 'Edit Prisoner Assistant Biomedical Research Entry' : 'New Prisoner Assistant Biomedical Research Entry')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
