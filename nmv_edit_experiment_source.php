<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('edit');

$form = new Form ('nmv_experiment_source');

// query: get experiment data
$experiment_id = (int) getUrlParameter('ID_experiment', 0);
$source_id = (int) getUrlParameter('ID_source', 0);

$experiment_name = 'Error: Unknown.';
$source_name = 'Error: Unknown.';

if ($experiment_id) {
    $querystring = "
    SELECT COALESCE(experiment_title, '') experiment_name
    FROM nmv__experiment
    WHERE ID_experiment = $experiment_id";
    $query = $dbi->connection->query($querystring);
    $experiment = $query->fetch_object();
    $experiment_name = $experiment->experiment_name;
} else if ($source_id) {
    $querystring = "
    SELECT CONCAT(COALESCE(source_title, ''), ' ', COALESCE(medium, '')) experiment_name
    FROM nmv__source
    WHERE ID_source = $source_id";
    $query = $dbi->connection->query($querystring);
    $source = $query->fetch_object();
    $source_name = $source->experiment_name;
} else {
    $exp_source_id = (int) getUrlParameter('ID_exp_source', 0);
    $querystring = "
    SELECT COALESCE(experiment_title, ' ') experiment_name,
        v.ID_experiment experiment_id
    FROM nmv__experiment v
    RIGHT JOIN nmv__experiment_source h ON (h.ID_experiment = v.ID_experiment)
    WHERE ID_exp_source = $exp_source_id";
    $query = $dbi->connection->query($querystring);
    $experiment = $query->fetch_object();
    $experiment_id = $experiment->experiment_id;
    $experiment_name = $experiment->experiment_name;
}

//query: get experiment-institutions for experiment SELECT
$querystring_experiment = "  SELECT e.ID_experiment AS value,
                                    CONCAT(IFNULL(e.experiment_title, 'no entry'), ' &ensp; - &ensp; ID ', e.ID_experiment, ' &ensp; - &ensp; ', IFNULL(i.institution_name, 'no entry')) AS title
                              FROM nmv__experiment e
                              LEFT JOIN nmv__institution i
                              ON e.ID_institution = i.ID_institution
                              ORDER BY title";

if ($source_id) {
    $form
    	->setLabel('Source: ' . $source_name);
} else {
    $form
    	->setLabel('Biomedical Research: ' . $experiment_name);
}

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_exp_source');

$form->addField ('ID_experiment',SELECT,REQUIRED)
    ->setLabel ('Biomedical Research')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromQuery ( "$querystring_experiment");
$form->addField ('ID_source',SELECT,REQUIRED)
    ->setLabel ('Source')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromTable ( 'nmv__source', 'ID_source', "LEFT(concat(IFNULL(LEFT(source_title, 60), '#'),' - ',IFNULL(LEFT(medium,40), '#'),' - ',IFNULL(creation_year, '#')),100)");
$form->addField ('location',TEXTAREA)
    ->setClass ('keyboardInput')
    ->setLabel ('Location');
$form->addField ('url',TEXTAREA)
    ->setClass ('keyboardInput')
    ->setLabel ('URL');
$form->addField ('access_day',TEXT,2)
		->addCondition(VALUE, MIN, 1)
		->addCondition(VALUE, MAX, 31)
    ->setLabel ('Access date DDMMYYYY');
$form->addField ('access_month',TEXT,2)
		->addCondition(VALUE, MIN, 1)
		->addCondition(VALUE, MAX, 12)
		->appendTo('access_day');
$form->addField ('access_year',TEXT,4)
		->appendTo('access_day');

$form
	->addButton (SUBMIT)
	->addButton (APPLY);

if ($source_id) {
  $form
  	->addAction (DATABASE,'nmv__experiment_source')
  	->addAction (REDIRECT,'nmv_list_experiment_source?ID_source='.$source_id);
} else {
$form
	->addAction (DATABASE,'nmv__experiment_source')
	->addAction (REDIRECT,'nmv_list_experiment_source?ID_experiment='.$experiment_id);
}

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Biomedical Research','nmv_list_experiments');
$dbi->addBreadcrumb ('Sources mentioning '.$experiment_name,'nmv_list_experiment_source?ID_experiment='.$experiment_id);

$layout
	->set('title',getUrlParameter('ID_exp_source') ? 'Edit Biomedical Research Source Entry' : 'New Biomedical Research Source Entry')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
