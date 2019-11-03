<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('edit');

$form = new Form ('nmv_experiment_literature');

// query: get experiment data
$experiment_id = (int) getUrlParameter('ID_experiment', 0);
$literature_id = (int) getUrlParameter('ID_literature', 0);

$experiment_name = 'Error: Unknown.';
$literature_name = 'Error: Unknown.';

if ($experiment_id) {
    $querystring = "
    SELECT CONCAT(COALESCE(experiment_title, ''), ' ', COALESCE(field_of_interest, '')) experiment_name
    FROM nmv__experiment
    WHERE ID_experiment = $experiment_id";
    $query = $dbi->connection->query($querystring);
    $experiment = $query->fetch_object();
    $experiment_name = $experiment->experiment_name;
} else if ($literature_id) {
    $querystring = "
    SELECT CONCAT(COALESCE(lit_title, ''), ' ', COALESCE(authors, '')) experiment_name
    FROM nmv__literature
    WHERE ID_literature = $literature_id";
    $query = $dbi->connection->query($querystring);
    $literature = $query->fetch_object();
    $literature_name = $literature->experiment_name;
} else {
    $exp_literature_id = (int) getUrlParameter('ID_exp_lit', 0);
    $querystring = "
    SELECT CONCAT(COALESCE(experiment_title, ''), ' ', COALESCE(field_of_interest, '')) experiment_name,
        v.ID_experiment experiment_id
    FROM nmv__experiment v
    RIGHT JOIN nmv__experiment_literature h ON (h.ID_experiment = v.ID_experiment)
    WHERE ID_exp_lit = $exp_literature_id";
    $query = $dbi->connection->query($querystring);
    $experiment = $query->fetch_object();
    $experiment_id = $experiment->experiment_id;
    $experiment_name = $experiment->experiment_name;
}

if ($literature_id) {
    $form
    	->setLabel('Biomedical Research: ' . $literature_name);
} else {
    $form
    	->setLabel('Literature: ' . $experiment_name);
}

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_exp_lit');

$form->addField ('ID_experiment',SELECT)
    ->setLabel ('Biomedical Research')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromTable ( 'nmv__experiment', 'ID_experiment', "LEFT(concat(IFNULL(LEFT(experiment_title, 60), '#'),' - ',IFNULL(LEFT(field_of_interest,40), '#'),' - ',IFNULL(objective, '#')),100)");
$form->addField ('ID_literature',SELECT)
    ->setLabel ('Literature')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromTable ( 'nmv__literature', 'ID_literature', "LEFT(concat(IFNULL(LEFT(lit_title, 60), '#'),' - ',IFNULL(LEFT(authors,40), '#'),' - ',IFNULL(lit_year, '#')),100)");
$form->addField ('pages',TEXT,50)
    ->setLabel ('Pages');

$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__experiment_literature')
	->addAction (REDIRECT,'nmv_list_experiment_literature?ID_experiment='.$experiment_id);

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Biomedical Research','nmv_list_experiments');
$dbi->addBreadcrumb ('Literature: '.$experiment_name,'nmv_list_experiment_literature?ID_experiment='.$experiment_id);

$layout
	->set('title',getUrlParameter('ID_exp_lit') ? 'Edit Biomedical Research Literature Entry' : 'New Biomedical Research Literature Entry')
	->set('content',$form->run().$form->debuglog->Show())
	->cast();
