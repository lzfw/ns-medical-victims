<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('edit');

$form = new Form ('nmv_perpetrator_experiment');

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
    $med_id = (int) getUrlParameter('ID_perp_exp', 0);
    $querystring = "
    SELECT CONCAT(COALESCE(v.surname, ''), ' ', COALESCE(v.first_names, '')) perpetrator_name,
        v.ID_perpetrator perpetrator_id
    FROM nmv__perpetrator v
    RIGHT JOIN nmv__perpetrator_experiment h ON (h.ID_perpetrator = v.ID_perpetrator)
    WHERE ID_perp_exp = $med_id";
    $query = $dbi->connection->query($querystring);
    $perpetrator = $query->fetch_object();
    $perpetrator_id = $perpetrator->perpetrator_id;
    $perpetrator_name = $perpetrator->perpetrator_name;
}


$form
	->setLabel('Biomedical Research: ' . $perpetrator_name);

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_perp_exp');

$form->addField ('ID_perpetrator',PROTECTED_TEXT)
    ->setLabel ('Perpetrator ID');
$form->addField ('ID_experiment',SELECT)
    ->setLabel ('Biomedical Research')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromTable ( 'nmv__experiment', 'ID_experiment', "LEFT(concat(IFNULL(LEFT(experiment_title, 60), '#'),' - ',IFNULL(LEFT(field_of_interest,40), '#'),' - ',IFNULL(funding, '#')),100)");

$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__perpetrator_experiment')
	->addAction (REDIRECT,'nmv_list_perpetrator_experiment?ID_perpetrator='.$perpetrator_id);

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Perpetrators','nmv_list_perpetrators');
$dbi->addBreadcrumb ('Biomedical Research: '.$perpetrator_name,'nmv_list_perpetrator_experiment?ID_perpetrator='.$perpetrator_id);

$layout
	->set('title',getUrlParameter('ID_perp_exp') ? 'Edit Perpetrator Biomedical Research Entry' : 'New Perpetrator Biomedical Research Entry')
	->set('content',$form->run().$form->debuglog->Show())
	->cast();
