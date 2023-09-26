<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('edit');

$form = new Form ('nmv_med_hist_tissue');

// query: get victim data for display on edit-page
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
    $med_id = (int) getUrlParameter('ID_med_history_tissue', 0);
    $querystring = "
    SELECT CONCAT(v.surname, ' ', v.first_names) victim_name,
        v.ID_victim victim_id
    FROM nmv__victim v
    RIGHT JOIN nmv__med_history_tissue h ON (h.ID_victim = v.ID_victim)
    WHERE ID_med_history_tissue = $med_id";
    $query = $dbi->connection->query($querystring);
    $victim = $query->fetch_object();
    $victim_id = $victim->victim_id;
    $victim_name = $victim->victim_name;
}


$form
	->setLabel('Brain tissue related to victim ' . $victim_name);

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_med_history_tissue');

$form->addField ('ID_victim',PROTECTED_TEXT)
    ->setLabel ('Victim ID');
$form->addField ('ID_tissue_form',SELECT,REQUIRED)
    ->setLabel ('Tissue Form')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromTable ( 'nmv__tissue_form', 'ID_tissue_form', "tissue_form");
$form->addField ('ID_tissue_state',SELECT,REQUIRED)
    ->setLabel ('Tissue State')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromTable ( 'nmv__tissue_state', 'ID_tissue_state', "tissue_state");
$form->addField('ID_institution', SELECT)
    ->setLabel('Institution')
    ->addOption(NO_VALUE, 'please choose')
    ->addOptionsFromTable('nmv__institution', 'ID_institution', "CONCAT(IFNULL(institution_name, '-/-'), '&emsp;---&emsp;', LEFT(IFNULL(location, '-/-'), 50))");
$form->addField ('since_day',TEXT,2)
    ->setLabel ('State Since DMYYYY')
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,31);
$form->addField ('since_month',TEXT,2)
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,12)
    ->appendTo('since_day');
$form->addField ('since_year',TEXT,4)
    ->addCondition(VALUE,MIN,0)
    ->appendTo('since_day');
$form->addField ('notes',TEXTAREA)
    ->setClass ('keyboardInput')
    ->setLabel ('Notes');
$form->addField ('ref_no',TEXT,50)
    ->setLabel ('Reference number');
$form->addField ('ref_no_2',TEXT,50)
    ->setLabel ('2nd Reference number');

$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__med_history_tissue')
	->addAction (REDIRECT,'nmv_view_med_hist_tissue?ID_med_history_tissue={ID_med_history_tissue}');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Victims','nmv_list_victims');
$dbi->addBreadcrumb ('Medical History of '.$victim_name,'nmv_list_med_hist?ID_victim='.$victim_id);

$layout
	->set('title',getUrlParameter('ID_med_history_tissue') ? 'Edit Brain Tissue' : 'New Brain Tissue')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
