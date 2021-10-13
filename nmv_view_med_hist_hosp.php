<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$dbi->setUserVar ('ID_med_history_hosp',getUrlParameter('ID_med_history_hosp'),NULL);
$med_hist_id = (int) getUrlParameter('ID_med_history_hosp',0);
$victim_id = 0;
$victim_name = 'Error: Unknown victim';

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Victims','nmv_list_victims');



// query: get victim data
$querystring = "
    SELECT
        v.ID_victim AS ID_victim,
        v.first_names AS first_names, v.surname AS surname, v.birth_place AS birth_place,
        CONCAT(
            IFNULL(i.institution_name, '#'),' - ',
            IFNULL(i.location, '#'),' - ',
            IFNULL(c.english, '#')) AS institution_l,
        o.english AS institution_order,
        diag.english AS diagnosis_l,
        e.english AS educational_abilities,
        b.english AS behaviour,
        d.english AS disability,
        CONCAT_WS('-', h.date_entry_year, h.date_entry_month, h.date_entry_day) AS date_entry,
        CONCAT_WS('-', h.date_exit_year, h.date_exit_month, h.date_exit_day) AS date_exit,
        h.age_entry AS age_entry, h.age_exit AS age_exit,
        h.institution AS institution, h.diagnosis AS diagnosis, h.autopsy_ref_no AS autopsy_ref_no,
        h.notes AS notes
    FROM nmv__med_history_hosp h
    LEFT JOIN nmv__victim v               ON h.ID_victim = v.ID_victim
    LEFT JOIN nmv__institution i           ON h.ID_institution = i.ID_institution
    LEFT JOIN nmv__institution_order o     ON h.ID_institution_order = o.ID_institution_order
    LEFT JOIN nmv__country c               ON c.ID_country = i.ID_country
    LEFT JOIN nmv__diagnosis diag          ON h.ID_diagnosis = diag.ID_diagnosis
    LEFT JOIN nmv__educational_abilities e ON h.ID_educational_abilities = e.ID_educational_abilities
    LEFT JOIN nmv__behaviour b             ON h.ID_behaviour = b.ID_behaviour
    LEFT JOIN nmv__disability d            ON h.ID_disability = d.ID_disability
    WHERE h.ID_med_history_hosp = ".$dbi->getUserVar('ID_med_history_hosp');
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
    $content .= '<tr><th>Hospitalization ID</th><td>'.
        htmlspecialchars($med_hist_id, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>Institution</th><td>'.
        htmlspecialchars($victim->institution_l, ENT_HTML5).
      '</td></tr>';
    $content .= '<tr><th>Institution Order</th><td>'.
            ($victim->institution_order
            ?htmlspecialchars($victim->institution_order, ENT_HTML5)
            :'')
        .'</td></tr>';
    $content .= '<tr><th>Diagnosis</th><td>'.
        htmlspecialchars($victim->diagnosis, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>Diagnosis Tags</th><td>'.
        htmlspecialchars($victim->diagnosis_l, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>Educational abilities</th><td>'.
        htmlspecialchars($victim->educational_abilities, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>Behaviour</th><td>'.
        htmlspecialchars($victim->behaviour, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>Disability</th><td>'.
        htmlspecialchars($victim->disability, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>Entry date</th><td>'.
        htmlspecialchars($victim->date_entry, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>Exit date</th><td>'.
        htmlspecialchars($victim->date_exit, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>Entry age</th><td>'.
        htmlspecialchars($victim->age_entry, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>Exit age</th><td>'.
        htmlspecialchars($victim->age_exit, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>Autopsy reference number</th><td>'.
        htmlspecialchars($victim->autopsy_ref_no, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>Notes / Autopsy details</th><td>'.
            htmlspecialchars($victim->notes, ENT_HTML5).'</td></tr>';

}
$content .= '</table>';

	$content .= '<div class="buttons">';
	if ($dbi->checkUserPermission('edit'))
	    $content .= createButton ('Edit','nmv_edit_med_hist_hosp?ID_med_history_hosp='.$med_hist_id,'icon edit');
	if ($dbi->checkUserPermission('admin'))
	    $content .= createButton(L_DELETE,'nmv_remove_med_hist_hosp?ID_med_history_hosp='.$med_hist_id,'icon delete');
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
