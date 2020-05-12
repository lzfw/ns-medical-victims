<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('edit');

$form = new Form ('nmv_edit_victim_other_names');

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
    $name_id = (int) getUrlParameter('ID_name', 0);
    $querystring = "
    SELECT CONCAT(v.surname, ' ', v.first_names) victim_name,
        v.ID_victim victim_id
    FROM nmv__victim v
    RIGHT JOIN nmv__victim_name vn ON (vn.ID_victim = v.ID_victim)
    WHERE ID_name = $name_id";
    $query = $dbi->connection->query($querystring);
    $victim = $query->fetch_object();
    $victim_id = $victim->victim_id;
    $victim_name = $victim->victim_name;
}


$form
	->setLabel('Names of ' . $victim_name);

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_name');

$form->addField ('ID_victim',PROTECTED_TEXT)
    ->setLabel ('victim ID');
$form->addField ('nametype',SELECT)
    ->setLabel ('name type')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromTable ( 'nmv__victim_nametype', 'ID_nametype', "english");
$form->addField ('victim_name',TEXT,250)
    ->setClass ('keyboardInput')
    ->setLabel ('name');
$form->addField ('victim_first_names',TEXT,50)
    ->setClass ('keyboardInput')
    ->setLabel ('first names');

$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__victim_name')
	->addAction (REDIRECT,'nmv_view_victim?ID_victim='.$victim_id);

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Victims','nmv_list_victims');
$dbi->addBreadcrumb ('Other names of '.$victim_name,'nmv_view_victim?ID_victim='.$victim_id);

$layout
	->set('title',getUrlParameter('ID_name') ? 'Edit Other Name' : 'New Name')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
