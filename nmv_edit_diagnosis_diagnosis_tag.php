<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('edit');

$form = new Form ('nmv_edit_diagnosis_diagnosis_tag');

// query: get experiment data
$ID_diagnosis = (int) getUrlParameter('ID_med_history_diagnosis', 0);


// query: get array of diagnosis tags of this diagnosis
$tag_array = array();
$tagged = $dbi->connection->query("SELECT dd.ID_diagnosis
                                   FROM nmv__diagnosis_diagnosis dd
                                   WHERE dd.ID_med_history_diagnosis = $ID_diagnosis");
$diagnosis = $dbi->connection->query("SELECT diagnosis FROM nmv__med_history_diagnosis WHERE ID_med_history_diagnosis = $ID_diagnosis")->fetch_object()->diagnosis;

while ($tag = $tagged->fetch_row()) {
	$tag_array[] = $tag[0];
}


//create create form
$form
	->addConnection(MYSQL_DB, $db_host, $db_user, $db_pass, $db_name)
	->setPrimaryKeyName('ID_diagnosis_diagnosis');

$form
  ->addField('ID_med_history_diagnosis', HIDDEN);

$form
  ->addField('ID_diagnosis', MULTICHECKBOX)
  ->setLabel('Diagnosis')
  ->addOptionsFromTable('nmv__diagnosis_tag', 'ID_diagnosis', 'diagnosis', $tag_array);

$form
  ->addButton(SUBMIT, 'Update Tags');

$form
	->addAction(DATABASE, 'nmv__diagnosis_diagnosis', 'tag')
  ->addAction(REDIRECT, 'nmv_view_med_hist_diagnosis?ID_med_history_diagnosis={ID_med_history_diagnosis}');

$dbi->addBreadcrumb(L_CONTENTS, 'z_menu_contents');

$layout
	->set('title', 'Tags (Diagnosis) for Diagnosis ID ' . $ID_diagnosis)
	->set('content', '<div>please select all diagnosis tags for the diagnosis, <br>then click button "Update Tags" at the bottom of the page</div>
                    <div>Freetext Diagnosis: '. $diagnosis . '</div>' . $form->run() . '<div class="message">' . $form->success_message . '</div>' . $form->debuglog->Show())
	->cast();
