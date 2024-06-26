<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('edit');

$form = new Form ('nmv_perpetrator_experiment');

$perp_exp_id = (int) getUrlParameter('ID_perp_exp', 0);
$perpetrator_id = (int) getUrlParameter('ID_perpetrator', 0);

// query: get perpetrator data
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
    $querystring = "
    SELECT CONCAT(COALESCE(p.surname, ''), ' ', COALESCE(p.first_names, '')) perpetrator_name,
        p.ID_perpetrator perpetrator_id
    FROM nmv__perpetrator p
    RIGHT JOIN nmv__perpetrator_experiment pe ON (pe.ID_perpetrator = p.ID_perpetrator)
    WHERE ID_perp_exp = $perp_exp_id";
    $query = $dbi->connection->query($querystring);
    $perpetrator = $query->fetch_object();
    $perpetrator_id = $perpetrator->perpetrator_id;
    $perpetrator_name = $perpetrator->perpetrator_name;
}

//query: get experiment-institutions for experiment SELECT
$querystring_experiment = "   SELECT e.ID_experiment AS value,
                                    CONCAT(IFNULL(e.experiment_title, 'no entry'), ' &ensp; - &ensp; ID ',
                                                  e.ID_experiment, ' &ensp; - &ensp; ',
                                                  IFNULL(GROUP_CONCAT(i.institution_name), 'no entry')) AS title
                              FROM nmv__experiment e
                              LEFT JOIN nmv__experiment_institution ei ON ei.ID_experiment = e.ID_experiment
                              LEFT JOIN nmv__institution i ON ei.ID_institution = i.ID_institution
                              GROUP BY e.ID_experiment
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
