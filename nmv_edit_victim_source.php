<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('edit');

$form = new Form ('nmv_victim_source');

// query: get victim data
$victim_id = (int) getUrlParameter('ID_victim', 0);
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
    $vict_source_id = (int) getUrlParameter('ID_vict_source', 0);
    $querystring = "
    SELECT CONCAT(COALESCE(v.surname, ''), ' ', COALESCE(v.first_names, '')) victim_name,
        v.ID_victim victim_id
    FROM nmv__victim v
    RIGHT JOIN nmv__victim_source h ON (h.ID_victim = v.ID_victim)
    WHERE ID_vict_source = $vict_source_id";
    $query = $dbi->connection->query($querystring);
    $victim = $query->fetch_object();
    $victim_id = $victim->victim_id;
    $victim_name = $victim->victim_name;
}


$form
	->setLabel('Source mentioning victim ' . $victim_name);

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_vict_source');

$form->addField ('ID_victim',PROTECTED_TEXT)
    ->setLabel ('victim ID');
$form->addField ('ID_source',SELECT, REQUIRED)
    ->setLabel ('source')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromTable ( 'nmv__source', 'ID_source', "LEFT(concat(IFNULL(LEFT(source_title, 60), '#'),' - ',IFNULL(LEFT(medium,40), '#'),' - ',IFNULL(creation_year, '#')),100)");
$form->addField ('location',TEXT,250)
    ->setClass ('keyboardInput')
    ->setLabel ('location');

$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__victim_source')
	->addAction (REDIRECT,'nmv_list_victim_source?ID_victim='.$victim_id);

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Victims','nmv_list_victims');
$dbi->addBreadcrumb ('Sources mentioning '.$victim_name,'nmv_list_victim_source?ID_victim='.$victim_id);

$layout
	->set('title',getUrlParameter('ID_vict_source') ? 'Edit Victim Source Entry' : 'New Victim Source Entry')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
