<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('edit');

$form = new Form ('nmv_edit_victim_evaluation');

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
    $name_id = (int) getUrlParameter('ID_evaluation', 0);
    $querystring = "
    SELECT CONCAT(v.surname, ' ', v.first_names) victim_name,
        v.ID_victim victim_id
    FROM nmv__victim v
    RIGHT JOIN nmv__evaluation e ON (e.ID_victim = v.ID_victim)
    WHERE ID_evaluation = $name_id";
    $query = $dbi->connection->query($querystring);
    $victim = $query->fetch_object();
    $victim_id = $victim->victim_id;
    $victim_name = $victim->victim_name;
}


$form
	->setLabel('Victim status evaluation of ' . $victim_name);

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_evaluation');

$form->addField ('ID_victim',PROTECTED_TEXT)
    ->setLabel ('ID person');
$form->addField ('evaluation_status',SELECT)
    ->setLabel ('Status')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromTable ( 'nmv__victim_evaluation_status', 'ID_evaluation_status', 'english');
$form->addField ('status_due_to',TEXTAREA)
    ->setClass ('keyboardInput')
    ->setLabel ('Status due to');
$form->addField ('status_notes',TEXTAREA)
    ->setClass ('keyboardInput')
    ->setLabel ('Status Notes');
$form->addField ('evaluation_list',TEXT,250)
    ->setLabel ('Evaluation List');


$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__evaluation')
	->addAction (REDIRECT,'nmv_view_victim?ID_victim='.$victim_id);

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Victims','nmv_list_victims');
$dbi->addBreadcrumb ('Evaluation of '.$victim_name,'nmv_view_victim?ID_victim='.$victim_id);

$layout
	->set('title',getUrlParameter('ID_evaluation') ? 'Edit Evaluation' : 'New Evaluation')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
