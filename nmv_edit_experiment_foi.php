<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('edit');

$form = new Form ('nmv_experiment_foi');

// query: get experiment data
$ID_experiment = (int) getUrlParameter('ID_experiment', 0);
$experiment_title = 'Error: Unknown.';
$title_query = $dbi->connection->query(" SELECT e.experiment_title
                                        FROM nmv__experiment e
                                        WHERE e.ID_experiment = $ID_experiment");
$experiment = $title_query->fetch_object();
$experiment_title = $experiment->experiment_title;

// query: get array of field_of_interest_tags of this experiment
$tag_array = array();
$tagged = $dbi->connection->query("SELECT ef.ID_foi
                                   FROM nmv__experiment_foi ef
                                   WHERE ef.ID_experiment = $ID_experiment");
while ($tag = $tagged->fetch_row()) {
	$tag_array[] = $tag[0];
}


//create create form
$form
	->addConnection(MYSQL_DB, $db_host, $db_user, $db_pass, $db_name)
	->setPrimaryKeyName('ID_exp_foi');

$form
  ->addField('ID_experiment', HIDDEN);

$form
  ->addField('ID_foi', MULTICHECKBOX)
  ->setLabel('Field of Interest')
  ->addOptionsFromTable('nmv__field_of_interest', 'ID_foi', 'english', $tag_array);

$form
  ->addButton(SUBMIT, 'Update Tags');

$form
//TODO .php beim redirect raus
	->addAction(DATABASE, 'nmv__experiment_foi', 'tag')
  ->addAction(REDIRECT, 'nmv_view_experiment?ID_experiment={ID_experiment}');

$dbi->addBreadcrumb(L_CONTENTS, 'z_menu_contents');
$dbi->addBreadcrumb('Biomedical Research', 'nmv_list_experiments');

$layout
	->set('title', 'Tags (Field of Interest) for Experiment ID ' . $ID_experiment . ' - ' . $experiment_title)
	->set('content', $form->run() . '<div class="message">' . $form->success_message . '</div>' . $form->debuglog->Show())
	->cast();
