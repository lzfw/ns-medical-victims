<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('edit');

$form = new Form ('nmv_edit_diagnosis_hosp_tag');

// query: get experiment data
$ID_hosp = (int) getUrlParameter('ID_med_history_hosp', 0);


// query: get array of diagnoses of this hospitalisation
$tag_array = array();
$tagged = $dbi->connection->query("SELECT dh.ID_diagnosis
                                   FROM nmv__diagnosis_hosp dh
                                   WHERE dh.ID_med_history_hosp = $ID_hosp");
$diagnosis = $dbi->connection->query("SELECT diagnosis FROM nmv__med_history_hosp WHERE ID_med_history_hosp = $ID_hosp")->fetch_object()->diagnosis;

while ($tag = $tagged->fetch_row()) {
	$tag_array[] = $tag[0];
}


//create create form
$form
	->addConnection(MYSQL_DB, $db_host, $db_user, $db_pass, $db_name)
	->setPrimaryKeyName('ID_hosp_diagnosis');

$form
  ->addField('ID_med_history_hosp', HIDDEN);

$form
  ->addField('ID_diagnosis', MULTICHECKBOX)
  ->setLabel('Diagnosis')
  ->addOptionsFromTable('nmv__diagnosis_tag', 'ID_diagnosis', 'diagnosis', $tag_array);

$form
  ->addButton(SUBMIT, 'Update Tags');

$form
	->addAction(DATABASE, 'nmv__diagnosis_hosp', 'tag')
  ->addAction(REDIRECT, 'nmv_view_med_hist_hosp?ID_med_history_hosp={ID_med_history_hosp}');

$dbi->addBreadcrumb(L_CONTENTS, 'z_menu_contents');

$layout
	->set('title', 'Tags (Diagnosis) for Hospitalisation ID ' . $ID_hosp)
	->set('content', '<div>please select all diagnoses for the hospitalisation, <br>then click button "Update Tags" at the bottom of the page</div>
                    <div>Freetext Diagnosis: '. $diagnosis . '</div>' . $form->run() . '<div class="message">' . $form->success_message . '</div>' . $form->debuglog->Show())
	->cast();
