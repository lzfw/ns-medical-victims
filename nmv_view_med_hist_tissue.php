<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$dbi->setUserVar ('ID_med_history_tissue',getUrlParameter('ID_med_history_tissue'),NULL);
$med_hist_id = (int) getUrlParameter('ID_med_history_tissue',0);
$victim_id = 0;
$victim_name = 'Error: Unknown victim';

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Victims','nmv_list_victims');



// query: get victim data
$querystring = "
    SELECT
        v.ID_victim ID_victim,
        v.first_names AS first_names, v.surname AS surname, v.birth_place AS birth_place,
        h.ID_med_history_tissue AS id, f.english AS tissue_form,
            s.english AS tissue_state, i.institution_name, i.location AS institution_location,
            CONCAT_WS('-', h.since_year, h.since_month, h.since_day) AS date,
            h.notes AS notes, h.ref_no AS ref_no
    FROM nmv__med_history_tissue h
    LEFT JOIN nmv__victim v                ON (h.ID_victim = v.ID_victim)
    LEFT JOIN nmv__tissue_form f           ON (f.ID_tissue_form = h.ID_tissue_form)
    LEFT JOIN nmv__tissue_state s          ON (s.ID_tissue_state = h.ID_tissue_state)
    LEFT JOIN nmv__institution i           ON (i.ID_institution = h.ID_institution)
    WHERE h.ID_med_history_tissue = ".$dbi->getUserVar('ID_med_history_tissue');
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
    $content .= '<tr><th>Tissue ID</th><td>'.
        htmlspecialchars($med_hist_id, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>Tissue form</th><td>'.
        htmlspecialchars($victim->tissue_form, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>Tissue state</th><td>'.
            htmlspecialchars($victim->tissue_state, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>State since</th><td>'.
        htmlspecialchars($victim->date, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>Tissue location<br>(Institution)</th><td>'.
        htmlspecialchars($victim->institution_name, ENT_HTML5) . '<br>in ' . htmlspecialchars($victim->institution_location, ENT_HTML5) . '</td></tr>';
    $content .= '<tr><th>Notes</th><td>'.
        htmlspecialchars($victim->notes, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>Reference number</th><td>'.
        htmlspecialchars($victim->ref_no, ENT_HTML5).'</td></tr>';

}
$content .= '</table>';

	$content .= '<div class="buttons">';
	if ($dbi->checkUserPermission('edit'))
	    $content .= createButton ('Edit','nmv_edit_med_hist_tissue?ID_med_history_tissue='.$med_hist_id,'icon edit');
	if ($dbi->checkUserPermission('admin'))
	    $content .= createButton(L_DELETE,'nmv_remove_med_hist_tissue?ID_med_history_tissue='.$med_hist_id,'icon delete');
	if ($victim_id) {
        $content .= createButton("basic victim data",'nmv_view_victim?ID_victim='.$victim_id,'icon report-paper');
	}
	$content .= '</div>';

if ($victim_id) {
    $content .= createBackLink ('Medical History: ' . $victim_name,'nmv_list_med_hist?ID_victim=' . $victim_id);
}

$layout
	->set('title','Brain Tissue: '.$victim_name)
	->set('content',$content)
	->cast();
