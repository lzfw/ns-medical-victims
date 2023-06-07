<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$dbi->setUserVar ('ID_med_history_hosp',getUrlParameter('ID_med_history_hosp'),NULL);
$ID_hosp = (int) getUrlParameter('ID_med_history_hosp',0);
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
        CONCAT(
            IFNULL(i.institution_name, '#'),' - ',
            IFNULL(i.location, '#'),' - ',
            IFNULL(c.country, '#')) AS institution,
        o.institution_order,
        e.educational_abilities AS educational_abilities,
        b.behaviour AS behaviour,
        d.disability AS disability,
        CONCAT(IFNULL(h.date_entry_day, '-'), '.', IFNULL(h.date_entry_month, '-'), '.', IFNULL(h.date_entry_year, '-')) AS date_entry,
        CONCAT(IFNULL(h.date_exit_day, '-'), '.', IFNULL(h.date_exit_month, '-'), '.', IFNULL(h.date_exit_year, '-')) AS date_exit,
        h.age_entry AS age_entry, h.age_exit AS age_exit, h.institution as institution_freetext,
        h.diagnosis AS diagnosis, h.autopsy_ref_no AS autopsy_ref_no,
        h.notes AS notes, IF(h.hosp_has_photo, 'yes', '-') AS photo
    FROM nmv__med_history_hosp h
    LEFT JOIN nmv__victim v               ON h.ID_victim = v.ID_victim
    LEFT JOIN nmv__institution i           ON h.ID_institution = i.ID_institution
    LEFT JOIN nmv__institution_order o     ON h.ID_institution_order = o.ID_institution_order
    LEFT JOIN nmv__country c               ON c.ID_country = i.ID_country
    LEFT JOIN nmv__educational_abilities e ON h.ID_educational_abilities = e.ID_educational_abilities
    LEFT JOIN nmv__behaviour b             ON h.ID_behaviour = b.ID_behaviour
    LEFT JOIN nmv__disability d            ON h.ID_disability = d.ID_disability
    WHERE h.ID_med_history_hosp = $ID_hosp";
$query = $dbi->connection->query($querystring);

//query get diagnosis tags
$tagged = $dbi->connection->query("SELECT d.diagnosis
                                   FROM nmv__diagnosis_hosp dh
                                   LEFT JOIN nmv__diagnosis_tag d ON d.ID_diagnosis = dh.ID_diagnosis
                                   WHERE dh.ID_med_history_hosp = $ID_hosp");
while ($tag = $tagged->fetch_row()) {
	$tag_array[] = $tag[0];
}
if($dbi->checkUserPermission('edit')): $tag_button = '<br>' . createButton('Click here to edit tags', 'nmv_edit_diagnosis_hosp_tag.php?ID_med_history_hosp=' . $ID_hosp, 'icon edit');
endif;

// $content .= '<table class="grid">';
if ($victim = $query->fetch_object()) {
    $victim_id = $victim->ID_victim;
    $victim_name = $victim->first_names . ' ' . $victim->surname;

    $dbi->addBreadcrumb ($victim_name,'nmv_view_victim?ID_victim=' . $victim_id);
    $dbi->addBreadcrumb ('Medical History','nmv_list_med_hist?ID_victim=' . $victim_id);

    $content = buildElement('table', 'grid',
      buildDataSheetRow('Victim ID',                      $victim->ID_victim) .
      buildDataSheetRow('Hospitalization ID',             $ID_hosp) .
      buildDataSheetRow('Institution',                    $victim->institution . ' --- '
                                                        . $victim->institution_freetext ).
      buildDataSheetRow('Institution Order',              $victim->institution_order) .
      buildDataSheetRow('Diagnosis',                      $victim->diagnosis) .
      buildDataSheetRowTag('Diagnosis Tags',              $tag_array, $tag_button) .
      buildDataSheetRow('Educational Abilities',          $victim->educational_abilities) .
      buildDataSheetRow('Behaviour',                      $victim->behaviour) .
      buildDataSheetRow('Disability',                     $victim->disability) .
      buildDataSheetRow('Entry Date ddmmyyyy',            $victim->date_entry) .
      buildDataSheetRow('Exit Date ddmmyyyy',             $victim->date_exit) .
      buildDataSheetRow('Entry Age',                      $victim->age_entry) .
      buildDataSheetRow('Exit Age',                       $victim->age_exit) .
      buildDataSheetRow('Autopsy Reference Number',       $victim->autopsy_ref_no) .
      buildDataSheetRow('Notes / Autopsy Details',        $victim->notes) .
      buildDataSheetRow('Medical Record contains Photo',  $victim->photo)
    );
}

	$content .= '<div class="buttons">';
	if ($dbi->checkUserPermission('edit'))
	    $content .= createButton ('Edit','nmv_edit_med_hist_hosp?ID_med_history_hosp='.$ID_hosp,'icon edit');
	if ($dbi->checkUserPermission('admin'))
	    $content .= createButton(L_DELETE,'nmv_remove_med_hist_hosp?ID_med_history_hosp='.$ID_hosp,'icon delete');
	if ($victim_id) {
        $content .= createButton("basic victim data",'nmv_view_victim?ID_victim='.$victim_id,'icon report-paper');
	}
	$content .= '</div>';

if ($victim_id) {
    $content .= createBackLink ('Medical History: ' . $victim_name,'nmv_list_med_hist?ID_victim=' . $victim_id);
}

$layout
	->set('title','Hospitalization History: '.$victim_name)
	->set('content',$content)
	->cast();
