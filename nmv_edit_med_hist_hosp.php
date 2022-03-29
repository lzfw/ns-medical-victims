<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission('edit');

$form = new Form ('nmv_med_hist_hosp');

// query: get victim data
$victim_id = (int) getUrlParameter('ID_victim', 0);
$med_id = (int) getUrlParameter('ID_med_history_hosp', 0);

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
    $querystring = "
    SELECT CONCAT(v.surname, ' ', v.first_names) victim_name,
        v.ID_victim victim_id
    FROM nmv__victim v
    RIGHT JOIN nmv__med_history_hosp h ON (h.ID_victim = v.ID_victim)
    WHERE ID_med_history_hosp = $med_id";
    $query = $dbi->connection->query($querystring);
    $victim = $query->fetch_object();
    $victim_id = $victim->victim_id;
    $victim_name = $victim->victim_name;
}


$form
	->setLabel('Hospitalization of ' . $victim_name);

$form
	->addConnection(MYSQL_DB, $db_host, $db_user, $db_pass, $db_name)
	->setPrimaryKeyName('ID_med_history_hosp');

$form->addField('ID_victim', PROTECTED_TEXT)
    ->setLabel('Victim ID');
$form->addField('ID_institution', SELECT, REQUIRED)
    ->setLabel('Institution')
    ->addOption(NO_VALUE, 'please choose')
    ->addOptionsFromTable( 'nmv__institution', 'ID_institution', "CONCAT(IFNULL(institution_name, '-/-'), '&emsp;---&emsp;', LEFT(IFNULL(location, '-/-'), 50))");
$form->addField('institution', TEXT, 70)
    ->setClass('keyboardInput')
    ->appendTo('ID_institution');
$form->addField('ID_institution_order', SELECT)
    ->setLabel('Institution Order')
    ->addOption(NO_VALUE,'please choose')
    ->addOptionsFromTable('nmv__institution_order', 'ID_institution_order', 'english');
$form->addField('diagnosis', TEXTAREA)
    ->setLabel('Diagnosis');
$form->addField('ID_educational_abilities', SELECT)
    ->setLabel('Educational Abilities')
    ->addOption(NO_VALUE, 'please choose')
    ->addOptionsFromTable('nmv__educational_abilities', 'ID_educational_abilities', 'english');
$form->addField('ID_behaviour', SELECT)
    ->setLabel('Behaviour')
    ->addOption(NO_VALUE, 'please choose')
    ->addOptionsFromTable ('nmv__behaviour', 'ID_behaviour', 'english');
$form->addField('ID_disability', SELECT)
    ->setLabel('Disability')
    ->addOption(NO_VALUE, 'please choose')
    ->addOptionsFromTable('nmv__disability', 'ID_disability', 'english');
$form->addField('date_entry_day', TEXT, 2)
    ->setLabel('Entry Date DMYYYY')
    ->addCondition(VALUE ,MIN, 0)
    ->addCondition(VALUE, MAX, 31);
$form->addField('date_entry_month', TEXT, 2)
    ->addCondition(VALUE, MIN, 0)
    ->addCondition(VALUE, MAX, 12)
    ->appendTo('date_entry_day');
$form->addField('date_entry_year', TEXT, 4)
    ->addCondition(VALUE, MIN, 0)
    ->addCondition(VALUE, MAX, 1950)
    ->appendTo('date_entry_day');
$form->addField('date_exit_day', TEXT, 2)
    ->setLabel('Exit Date DMYYYY')
    ->addCondition(VALUE, MIN, 0)
    ->addCondition(VALUE, MAX, 31);
$form->addField('date_exit_month', TEXT, 2)
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,12)
    ->appendTo('date_exit_day');
$form->addField('date_exit_year',TEXT,4)
    ->addCondition(VALUE, MIN, 0)
    ->addCondition(VALUE, MAX, 2099)
    ->appendTo('date_exit_day');
$form->addField('age_entry', TEXT, 3)
    ->setLabel('Entry Age')
    ->addCondition(VALUE, MIN, 0)
    ->addCondition(VALUE, MAX, 199);
$form->addField('age_exit', TEXT, 3)
    ->setLabel('Exit Age')
    ->addCondition(VALUE, MIN, 0)
    ->addCondition(VALUE, MAX, 199);
$form->addField('autopsy_ref_no', TEXT, 50)
    ->setLabel('Autopsy reference number');
$form->addField('notes', TEXTAREA)
    ->setClass('keyboardInput')
    ->setLabel('Notes / Autopsy Details');
$form->addField('info', STATIC_TEXT, '<hr><strong>Diagnosis Tags </strong> can be edited in the Hospitalisation View. <br>
    Click OK-Button in order to save your changes and switch to View.<hr>');

$form
	->addButton(SUBMIT)
	->addButton(APPLY);

$form
	->addAction(DATABASE, 'nmv__med_history_hosp')
	->addAction(REDIRECT, 'nmv_view_med_hist_hosp?ID_med_history_hosp={ID_med_history_hosp}');

$dbi->addBreadcrumb(L_CONTENTS, 'z_menu_contents');
$dbi->addBreadcrumb('Victims', 'nmv_list_victims');
$dbi->addBreadcrumb('Medical History of '.$victim_name, 'nmv_list_med_hist?ID_victim='.$victim_id);

$layout
	->set('title',getUrlParameter('ID_med_history_hosp') ? 'Edit Hospitalization' : 'New Hospitalization')
	->set('content', $form->run() . '<div class="message">' . $form->success_message . '</div>' . $form->debuglog->Show())
	->cast();
