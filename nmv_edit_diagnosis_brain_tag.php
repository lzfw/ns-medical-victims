<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('edit');

$form = new Form ('nmv_edit_diagnosis_brain_tag');

// query: get experiment data
$ID_brain = (int) getUrlParameter('ID_med_history_brain', 0);


// query: get array of diagnoses of this brain report
$tag_array = array();
$tagged = $dbi->connection->query("SELECT db.ID_diagnosis
                                   FROM nmv__diagnosis_brain db
                                   WHERE db.ID_med_history_brain = $ID_brain");
while ($tag = $tagged->fetch_row()) {
	$tag_array[] = $tag[0];
}


//create create form
$form
	->addConnection(MYSQL_DB, $db_host, $db_user, $db_pass, $db_name)
	->setPrimaryKeyName('ID_brain_diagnosis');

$form
  ->addField('ID_med_history_brain', HIDDEN);

$form
  ->addField('ID_diagnosis', MULTICHECKBOX)
  ->setLabel('Diagnosis')
  ->addOptionsFromTable('nmv__diagnosis_tag', 'ID_diagnosis', 'diagnosis', $tag_array);

$form
  ->addButton(SUBMIT, 'Update Tags');

$form
	->addAction(DATABASE, 'nmv__diagnosis_brain', 'tag')
  ->addAction(REDIRECT, 'nmv_view_med_hist_brain?ID_med_history_brain={ID_med_history_brain}');

$dbi->addBreadcrumb(L_CONTENTS, 'z_menu_contents');

$layout
	->set('title', 'Tags (Diagnosis) for Brain report ID ' . $ID_brain)
	->set('content','<div>please select all diagnoses for the brain report, <br>then click button "Update Tags" at the bottom of the page</div>' . $form->run() . '<div class="message">' . $form->success_message . '</div>' . $form->debuglog->Show())
	->cast();
