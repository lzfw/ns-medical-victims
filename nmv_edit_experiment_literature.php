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
    SELECT COALESCE(experiment_title, '') experiment_name
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
    SELECT COALESCE(experiment_title, '') experiment_name,
        v.ID_experiment experiment_id
    FROM nmv__experiment v
    RIGHT JOIN nmv__experiment_literature h ON (h.ID_experiment = v.ID_experiment)
    WHERE ID_exp_lit = $exp_literature_id";
    $query = $dbi->connection->query($querystring);
    $experiment = $query->fetch_object();
    $experiment_id = $experiment->experiment_id;
    $experiment_name = $experiment->experiment_name;
}
//query: get experiment-institutions for experiment SELECT
$querystring_experiment = "  SELECT e.ID_experiment AS value, CONCAT(IFNULL(e.experiment_title, 'no entry'), ' &ensp; - &ensp; ID ', e.ID_experiment, ' &ensp; - &ensp; ', IFNULL(i.institution_name, 'no entry')) AS title
                              FROM nmv__experiment e
                              LEFT JOIN nmv__institution i
                              ON e.ID_institution = i.ID_institution
                              ORDER BY title";

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

$form->addField ('ID_experiment',SELECT,REQUIRED)
    ->setLabel ('Biomedical Research')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromQuery ( "$querystring_experiment");
$form->addField ('ID_literature',SELECT,REQUIRED)
    ->setLabel ('Literature')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromTable ( 'nmv__literature', 'ID_literature', "LEFT(concat(IFNULL(LEFT(lit_title, 60), '#'),' - ',IFNULL(LEFT(authors,40), '#'),' - ',IFNULL(lit_year, '#')),100)");
$form->addField ('pages',TEXT,50)
    ->setLabel ('Pages');
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

$form
	->addAction (DATABASE,'nmv__experiment_literature');
if($experiment_id){
    $form
        ->addAction (REDIRECT,'nmv_list_experiment_literature?ID_experiment='.$experiment_id);
}
elseif($literature_id){
    $form
        ->addAction (REDIRECT,'nmv_list_experiment_literature?ID_literature='.$literature_id);
}

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Biomedical Research','nmv_list_experiments');
$dbi->addBreadcrumb ('Literature: '.$experiment_name,'nmv_list_experiment_literature?ID_experiment='.$experiment_id);

$layout
	->set('title',getUrlParameter('ID_exp_lit') ? 'Edit Biomedical Research Literature Entry' : 'New Biomedical Research Literature Entry')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
