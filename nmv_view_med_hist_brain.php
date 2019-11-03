<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$dbi->setUserVar ('ID_med_history_brain',getUrlParameter('ID_med_history_brain'),NULL);
$med_hist_id = (int) getUrlParameter('ID_med_history_brain',0);
$victim_id = 0;
$victim_name = 'Error: Unknown victim';

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Victims','nmv_list_victims');



// query: get victim data
$querystring = "
    SELECT
        v.ID_victim ID_victim,
        v.first_names first_names, v.surname surname, v.birth_place birth_place,
        LEFT(CONCAT(
            IFNULL(LEFT(i.institution_name, 60), '#'),' - ',
            IFNULL(LEFT(i.location,40), '#'),' - ',
            IFNULL(i.country, '#')),100) institution,
        diag.english as diagnosis_l,
        CONCAT_WS('-', h.brain_report_year, h.brain_report_month, h.brain_report_day) brain_report_date,
        h.kwi_researcher kwi_researcher, h.diagnosis diagnosis,
        h.notes notes, h.ref_no ref_no
    FROM nmv__med_history_brain h
    LEFT JOIN nmv__victim v               ON (h.ID_victim = v.ID_victim)
    LEFT JOIN nmv__institution i           ON (h.ID_institution = i.ID_institution)
    LEFT JOIN nmv__diagnosis diag          ON (h.ID_diagnosis = diag.ID_diagnosis)
    WHERE h.ID_med_history_brain = ".$dbi->getUserVar('ID_med_history_brain');
$query = $dbi->connection->query($querystring);

$content = '';
$content .= '<table class="grid">';
if ($victim = $query->fetch_object()) {
    $victim_id = $victim->ID_victim;
    $victim_name = $victim->first_names . ' ' . $victim->surname;

    $dbi->addBreadcrumb ($victim_name,'nmv_view_victim?ID_victim=' . $victim_id);
    $dbi->addBreadcrumb ('Medical History','nmv_list_med_hist?ID_victim=' . $victim_id);
    
    $content .= '<tr><th>Victim ID</th><td>'.
        htmlspecialchars($victim->ID_victim, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>Brain Research ID</th><td>'.
        htmlspecialchars($med_hist_id, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>Institution</th><td>'.
        htmlspecialchars($victim->institution, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>KWI researcher</th><td>'.
            htmlspecialchars($victim->kwi_researcher, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>Diagnosis</th><td>'.
        htmlspecialchars($victim->diagnosis_l, ENT_HTML5). ' <br>'.
        htmlspecialchars($victim->diagnosis, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>Report date</th><td>'.
        htmlspecialchars($victim->brain_report_date, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>Notes</th><td>'.
        htmlspecialchars($victim->notes, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>Reference number</th><td>'.
        htmlspecialchars($victim->ref_no, ENT_HTML5).'</td></tr>';

}
$content .= '</table>';

	$content .= '<div class="buttons">';
	if ($dbi->checkUserPermission('edit'))
	    $content .= createButton ('Edit','nmv_edit_med_hist_brain?ID_med_history_brain='.$med_hist_id,'icon edit');
	if ($dbi->checkUserPermission('admin'))
	    $content .= createButton(L_DELETE,'nmv_remove_med_hist_brain?ID_med_history_brain='.$med_hist_id,'icon delete');
	if ($victim_id) {
        $content .= createButton("basic victim data",'nmv_view_victim?ID_victim='.$victim_id,'icon report-paper');
	}
	$content .= '</div>';
	
if ($victim_id) {
    $content .= createBackLink ('Medical History: ' . $victim_name,'nmv_list_med_hist?ID_victim=' . $victim_id);
}

$layout
	->set('title','Brain History: '.$victim_name)
	->set('content',$content)
	->cast();
