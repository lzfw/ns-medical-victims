<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

//$dbi->setUserVar ('ID_med_history_brain',getUrlParameter('ID_med_history_brain'),NULL);
$ID_brain = (int) getUrlParameter('ID_med_history_brain',0);
$victim_id = 0;
$victim_name = 'Error: Unknown victim';
$tag_array = array();
$tag_button = '';
$content = '';


$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Victims','nmv_list_victims');



// query: get victim data
$querystring = "SELECT v.ID_victim ID_victim,
        v.first_names first_names, v.surname surname, v.birth_place birth_place,
        CONCAT(
            IFNULL(i.institution_name, '#'),' - ',
            IFNULL(i.location, '#'),' - ',
            IFNULL(c.english, '#'))institution,
        diag.english as diagnosis_l,
        CONCAT(IFNULL(h.brain_report_day, '-'), '.', IFNULL(h.brain_report_month, '-'), '.', IFNULL(h.brain_report_year, '-')) AS brain_report_date,
        h.kwi_researcher kwi_researcher, h.diagnosis diagnosis,
        h.notes notes, h.ref_no ref_no
    FROM nmv__med_history_brain h
    LEFT JOIN nmv__victim v               ON (h.ID_victim = v.ID_victim)
    LEFT JOIN nmv__institution i           ON (h.ID_institution = i.ID_institution)
    LEFT JOIN nmv__country c               ON (c.ID_country = i.ID_country)
    LEFT JOIN nmv__diagnosis diag          ON (h.ID_diagnosis = diag.ID_diagnosis)
    WHERE h.ID_med_history_brain = $ID_brain";
$query = $dbi->connection->query($querystring);

//query get diagnosis tags
$tagged = $dbi->connection->query("SELECT d.diagnosis
                                   FROM nmv__diagnosis_brain db
                                   LEFT JOIN nmv__diagnosis_tag d ON d.ID_diagnosis = db.ID_diagnosis
                                   WHERE db.ID_med_history_brain = $ID_brain");
while ($tag = $tagged->fetch_row()) {
	$tag_array[] = $tag[0];
}
if($dbi->checkUserPermission('edit')): $tag_button = '<br>' . createButton('Click here to edit tags', 'nmv_edit_diagnosis_brain_tag.php?ID_med_history_brain=' . $ID_brain, 'icon edit');
endif;

if ($victim = $query->fetch_object()) {
    $victim_id = $victim->ID_victim;
    $victim_name = $victim->first_names . ' ' . $victim->surname;

    $dbi->addBreadcrumb ($victim_name,'nmv_view_victim?ID_victim=' . $victim_id);
    $dbi->addBreadcrumb ('Medical History','nmv_list_med_hist?ID_victim=' . $victim_id);

    $content = buildElement('table', 'grid',
      buildDataSheetRow('Victim ID',                          $victim->ID_victim) .
      buildDataSheetRow('Brain Report ID',                    $ID_brain) .
      buildDataSheetRow('Institution',                        $victim->institution) .
      buildDataSheetRow('KWI researcher',                     $victim->kwi_researcher) .
      buildDataSheetRow('Diagnosis',                          $victim->diagnosis) .
      buildDataSheetRowTag('Diagnosis Tags',                  $tag_array, $tag_button) .
      buildDataSheetRow('Report date dmyyyy',                 $victim->brain_report_date) .
      buildDataSheetRow('Notes',                              $victim->notes) .
      buildDataSheetRow('Reference number',                   $victim->ref_no)


    );
}

	$content .= '<div class="buttons">';
	if ($dbi->checkUserPermission('edit'))
	    $content .= createButton ('Edit','nmv_edit_med_hist_brain?ID_med_history_brain='.$ID_brain,'icon edit');
	if ($dbi->checkUserPermission('admin'))
	    $content .= createButton(L_DELETE,'nmv_remove_med_hist_brain?ID_med_history_brain='.$ID_brain,'icon delete');
	if ($victim_id) {
        $content .= createButton("basic victim data",'nmv_view_victim?ID_victim='.$victim_id,'icon report-paper');
	}
	$content .= '</div>';

if ($victim_id) {
    $content .= createBackLink ('Medical History: ' . $victim_name,'nmv_list_med_hist?ID_victim=' . $victim_id);
}

$layout
	->set('title','Brain Report: '.$victim_name)
	->set('content',$content)
	->cast();
