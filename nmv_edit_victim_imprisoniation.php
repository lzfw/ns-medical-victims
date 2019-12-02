<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('edit');

$form = new Form ('nmv_edit_victim_imprisoniation');

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
    $name_id = (int) getUrlParameter('ID_imprisoniation', 0);
    $querystring = "
    SELECT CONCAT(v.surname, ' ', v.first_names) victim_name,
        v.ID_victim victim_id
    FROM nmv__victim v
    RIGHT JOIN nmv__imprisoniation i ON (i.ID_victim = v.ID_victim)
    WHERE ID_imprisoniation = $name_id";
    $query = $dbi->connection->query($querystring);
    $victim = $query->fetch_object();
    $victim_id = $victim->victim_id;
    $victim_name = $victim->victim_name;
}


$form
	->setLabel('Victim Classification: ' . $victim_name);

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_imprisoniation');

$form->addField ('ID_victim',PROTECTED_TEXT)
    ->setLabel ('Victim ID');
$form->addField ('ID_classification',SELECT)
    ->setLabel ('Classification')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromTable ( 'nmv__victim_classification', 'ID_classification', "english");
$form->addField ('number',TEXT,50) 
    ->setLabel ('(Prison) Number');
$form->addField ('location',TEXT,50) 
    ->setLabel ('Location');

$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__imprisoniation')
	->addAction (REDIRECT,'nmv_view_victim?ID_victim='.$victim_id);

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Victims','nmv_list_victims');
$dbi->addBreadcrumb ('Victim Classification: '.$victim_name,'nmv_view_victim?ID_victim='.$victim_id);

$layout
	->set('title',getUrlParameter('ID_imprisoniation') ? 'Edit Victim Classification' : 'New Victim Classification')
	->set('content',$form->run().$form->debuglog->Show())
	->cast();