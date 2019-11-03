<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('edit');

$form = new Form ('nmv_perpetrator_source');

// query: get perpetrator data
$perpetrator_id = (int) getUrlParameter('ID_perpetrator', 0);
$perpetrator_name = 'Error: Unknown.';
if ($perpetrator_id) {
    $querystring = "
    SELECT CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_names, '')) perpetrator_name
    FROM nmv__perpetrator
    WHERE ID_perpetrator = $perpetrator_id";
    $query = $dbi->connection->query($querystring);
    $perpetrator = $query->fetch_object();
    $perpetrator_name = $perpetrator->perpetrator_name;
} else {
    $vict_source_id = (int) getUrlParameter('ID_perp_source', 0);
    $querystring = "
    SELECT CONCAT(COALESCE(v.surname, ''), ' ', COALESCE(v.first_names, '')) perpetrator_name,
        v.ID_perpetrator perpetrator_id
    FROM nmv__perpetrator v
    RIGHT JOIN nmv__perpetrator_source h ON (h.ID_perpetrator = v.ID_perpetrator)
    WHERE ID_perp_source = $vict_source_id";
    $query = $dbi->connection->query($querystring);
    $perpetrator = $query->fetch_object();
    $perpetrator_id = $perpetrator->perpetrator_id;
    $perpetrator_name = $perpetrator->perpetrator_name;
}


$form
	->setLabel('Source mentioning perpetrator ' . $perpetrator_name);

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_perp_source');

$form->addField ('ID_perpetrator',PROTECTED_TEXT)
    ->setLabel ('Perpetrator ID');
$form->addField ('ID_source',SELECT)
    ->setLabel ('Source')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromTable ( 'nmv__source', 'ID_source', "LEFT(concat(IFNULL(LEFT(source_title, 60), '#'),' - ',IFNULL(LEFT(medium,40), '#'),' - ',IFNULL(creation_year, '#')),100)");
$form->addField ('location',TEXT,250)
    ->setLabel ('Location');

$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__perpetrator_source')
	->addAction (REDIRECT,'nmv_list_perpetrator_source?ID_perpetrator='.$perpetrator_id);

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Perpetrators','nmv_list_perpetrators');
$dbi->addBreadcrumb ('Sources mentioning '.$perpetrator_name,'nmv_list_perpetrator_source?ID_perpetrator='.$perpetrator_id);

$layout
	->set('title',getUrlParameter('ID_perp_source') ? 'Edit Perpetrator Source Entry' : 'New Perpetrator Source Entry')
	->set('content',$form->run().$form->debuglog->Show())
	->cast();
