<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('edit');

$form = new Form ('nmv_experiment_institution');

// query: get experiment data
$ID_experiment = (int) getUrlParameter('ID_experiment', 0);
$experiment_title = 'Error: Unknown.';
$title_query = $dbi->connection->query(" SELECT e.experiment_title
                                        FROM nmv__experiment e
                                        WHERE e.ID_experiment = $ID_experiment");
$experiment = $title_query->fetch_object();
$experiment_title = $experiment->experiment_title;

// query: get array of institutions of this experiment
$institution_array = array();
$located = $dbi->connection->query("SELECT ei.ID_institution
                                   FROM nmv__experiment_institution ei
                                   WHERE ei.ID_experiment = $ID_experiment");
while ($institution = $located->fetch_row()) {
	$institution_array[] = $institution[0];
}


//create form
$form
	->addConnection(MYSQL_DB, $db_host, $db_user, $db_pass, $db_name)
	->setPrimaryKeyName('ID_exp_institution');

$form
  ->addField('ID_experiment', HIDDEN);

$form
  ->addField('ID_institution', MULTICHECKBOX)
  ->setLabel('Institution')
  ->addOptionsFromTable('nmv__institution', 'ID_institution', 'institution_name', $institution_array);

$form
  ->addButton(SUBMIT, 'Update institutions');

$form
	->addAction(DATABASE, 'nmv__experiment_institution', 'tag')
  ->addAction(REDIRECT, 'nmv_view_experiment?ID_experiment={ID_experiment}');

$dbi->addBreadcrumb(L_CONTENTS, 'z_menu_contents');
$dbi->addBreadcrumb('Biomedical Research', 'nmv_list_experiments');

$layout
	->set('title', 'Institutions for Experiment ID ' . $ID_experiment . ' - ' . $experiment_title)
	->set('content', '<div>please select all institutions for the experiment and then click button "Update institutions"</div>' . $form->run() . '<div class="message">' . $form->success_message . '</div>' . $form->debuglog->Show())
	->cast();
