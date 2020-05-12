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

//query: get experiment-institutions for experiment SELECT
$querystring_experiment = "  SELECT e.ID_experiment AS value, CONCAT(IFNULL(e.experiment_title, 'no entry'), ' &ensp; - &ensp; ID ', e.ID_experiment, ' &ensp; - &ensp; ', IFNULL(i.institution_name, 'no entry')) AS title
                              FROM nmv__experiment e
                              LEFT JOIN nmv__institution i
                              ON e.ID_institution = i.ID_institution
                              ORDER BY title";


$form
	->setLabel('Biomedical Research: ' . $perpetrator_name);

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_perp_exp');

$form->addField ('ID_perpetrator',PROTECTED_TEXT)
    ->setLabel ('Perpetrator ID');
$form->addField ('ID_experiment',SELECT,REQUIRED)
    ->setLabel ('Biomedical Research')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromQuery ( "$querystring_experiment");

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
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
