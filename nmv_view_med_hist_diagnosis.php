<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$dbi->setUserVar ('ID_med_history_diagnosis',getUrlParameter('ID_med_history_diagnosis'),NULL);
$ID_diagnosis = (int) getUrlParameter('ID_med_history_diagnosis',0);
$victim_id = 0;
$victim_name = 'Error: Unknown victim';
$tag_array = array();
$tag_button = '';
$content = '';


$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Victims','nmv_list_victims');



// query: get victim data
$querystring = "SELECT v.ID_victim AS ID_victim,
        v.first_names AS first_names, v.surname AS surname, v.birth_place AS birth_place,
        d.diagnosis AS diagnosis, d.year AS year
    FROM nmv__med_history_diagnosis d
    LEFT JOIN nmv__victim v ON d.ID_victim = v.ID_victim
    WHERE d.ID_med_history_diagnosis = $ID_diagnosis";
$query = $dbi->connection->query($querystring);

//query get diagnosis tags
$tagged = $dbi->connection->query("SELECT d.diagnosis
                                   FROM nmv__diagnosis_diagnosis dd
                                   LEFT JOIN nmv__diagnosis_tag d ON d.ID_diagnosis = dd.ID_diagnosis
                                   WHERE dd.ID_med_history_diagnosis = $ID_diagnosis");
while ($tag = $tagged->fetch_row()) {
	$tag_array[] = $tag[0];
}
if($dbi->checkUserPermission('edit')): $tag_button = '<br>' . createButton('Click here to edit tags', 'nmv_edit_diagnosis_diagnosis_tag.php?ID_med_history_diagnosis=' . $ID_diagnosis, 'icon edit');
endif;

// $content .= '<table class="grid">';
if ($victim = $query->fetch_object()) {
    $victim_id = $victim->ID_victim;
    $victim_name = $victim->first_names . ' ' . $victim->surname;

    $dbi->addBreadcrumb ($victim_name,'nmv_view_victim?ID_victim=' . $victim_id);
    $dbi->addBreadcrumb ('Medical History','nmv_list_med_hist?ID_victim=' . $victim_id);

    $content = buildElement('table', 'grid',
      buildDataSheetRow('Victim ID',                      $victim->ID_victim) .
      buildDataSheetRow('Diagnosis ID',                   $ID_diagnosis) .
      buildDataSheetRow('Year',                           $victim->year) .
      buildDataSheetRow('Diagnosis',                      $victim->diagnosis) .
      buildDataSheetRowTag('Diagnosis Tags',              $tag_array, $tag_button)

    );
}

	$content .= '<div class="buttons">';
	if ($dbi->checkUserPermission('edit'))
	    $content .= createButton ('Edit','nmv_edit_med_hist_diagnosis?ID_med_history_diagnosis='.$ID_diagnosis,'icon edit');
	if ($dbi->checkUserPermission('admin'))
	    $content .= createButton(L_DELETE,'nmv_remove_med_hist_hosp?ID_med_history_diagnosis='.$ID_diagnosis,'icon delete');
	if ($victim_id) {
        $content .= createButton("basic victim data",'nmv_view_victim?ID_victim='.$victim_id,'icon report-paper');
	}
	$content .= '</div>';

if ($victim_id) {
    $content .= createBackLink ('Medical History: ' . $victim_name,'nmv_list_med_hist?ID_victim=' . $victim_id);
}

$layout
	->set('title','Diagnoses: '.$victim_name)
	->set('content',$content)
	->cast();
