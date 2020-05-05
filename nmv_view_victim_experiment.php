<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$dbi->setUserVar ('ID_vict_exp',getUrlParameter('ID_vict_exp'),NULL);
$vict_exp_id = (int) getUrlParameter('ID_vict_exp',0);
$victim_id = 0;
$victim_name = 'Error: Unknown victim';
$experiment_title = 'Error: Unknown biomedical research';
$experiment_id = 0;

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Victims','nmv_list_victims');



// query: get victim data
$querystring = "
    SELECT
        ve.ID_victim ID_victim, ve.ID_experiment,
        v.first_names first_names, v.surname surname, v.birth_place birth_place,
        LEFT(concat(IFNULL(LEFT(e.experiment_title, 60), '#'),' - ',IFNULL(LEFT(e.field_of_interest,40), '#'),' - ',IFNULL(e.funding, '#')),100) experiment,
        ve.experiment_duration experiment_duration, ve.age_experiment_start age_experiment_start,
        ve.outcome_injuries outcome_injuries, ve.not_corroborated not_corroborated,
        ve.notes_perpetrator notes_perpetrator, ve.narratives narratives,
        CONCAT_WS('-', ve.exp_start_year, ve.exp_start_month, ve.exp_start_day) ve_start_date,
        CONCAT_WS('-', ve.exp_end_year, ve.exp_end_month, ve.exp_end_day) ve_end_date,
        ve.notes notes,
        CONCAT(IFNULL(e.experiment_title, 'no entry'), ' - ', IFNULL(e.field_of_interest, 'no entry'), ' - ID ',
                      e.ID_experiment, ' - ',
                      IFNULL(i.institution_name, 'no entry')) as ei_info,
        s.english survival
    FROM nmv__victim_experiment ve
    LEFT JOIN nmv__victim v                ON (ve.ID_victim = v.ID_victim)
    LEFT JOIN nmv__experiment e            ON (ve.ID_experiment = e.ID_experiment)
    LEFT JOIN nmv__institution i           ON (e.ID_institution = i.ID_institution)
    LEFT JOIN nmv__survival s              ON (ve.ID_survival = s.ID_survival)
    WHERE ve.ID_vict_exp = ".$dbi->getUserVar('ID_vict_exp');
$query = $dbi->connection->query($querystring);

$content = '';
$content .= '<table class="grid">';
if ($ve = $query->fetch_object()) {
    $victim_id = $ve->ID_victim;
    $victim_name = $ve->first_names . ' ' . $ve->surname;
    $experiment_title = $ve->experiment;
    $experiment_id = $ve->ID_experiment;
    $ei_info = $ve->ei_info;

    $dbi->addBreadcrumb ($victim_name,'nmv_view_victim?ID_victim=' . $victim_id);
    $dbi->addBreadcrumb ('Biomedical Research','nmv_list_victim_experiment?ID_victim=' . $victim_id);

    $content .= '<tr><th>Victim ID</th><td>'.
        htmlspecialchars($victim_id, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>ID Victim - Experiment</th><td>'.
        htmlspecialchars($vict_exp_id, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>Victim</th><td><a href="nmv_view_victim?ID_victim='. $victim_id . '">' .
        htmlspecialchars($victim_name, ENT_HTML5).'</a></td></tr>';
    $content .= '<tr><th>Biomedical Research <br> (title, field of interest, ID, institution)</th><td><a href="nmv_view_experiment?ID_experiment='. $experiment_id . '">' .
        htmlspecialchars($ei_info, ENT_HTML5).'</a></td></tr>';
    $content .= '<tr><th>Biomedical Research Duration</th><td>'.
        htmlspecialchars($ve->experiment_duration, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>Start and End Date</th><td>'.
        htmlspecialchars($ve->ve_start_date . ' - ' . $ve->ve_end_date, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>Age (experiment start)</th><td>'.
        htmlspecialchars($ve->age_experiment_start, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>Injuries</th><td>'.
        htmlspecialchars($ve->outcome_injuries, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>Survival</th><td>'.
        htmlspecialchars($ve->survival, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>Not Corroborated</th><td>'.
        htmlspecialchars($ve->not_corroborated ? 'Yes' : 'No', ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>Notes about Perpetrator</th><td>'.
        htmlspecialchars($ve->notes_perpetrator, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>Narratives</th><td>'.
        htmlspecialchars($ve->narratives, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>Notes</th><td>'.
        htmlspecialchars($ve->notes, ENT_HTML5).'</td></tr>';
}
$content .= '</table>';

	$content .= '<div class="buttons">';
	if ($dbi->checkUserPermission('edit'))
	    $content .= createButton ('Edit','nmv_edit_victim_experiment?ID_vict_exp='.$vict_exp_id,'icon edit');
	if ($dbi->checkUserPermission('admin'))
	    $content .= createButton(L_DELETE,'nmv_remove_victim_experiment?ID_vict_exp='.$vict_exp_id,'icon delete');
	if ($victim_id) {
        $content .= createBackLink ('Biomedical research: Victim ' . $victim_name,'nmv_list_victim_experiment?ID_victim='.$victim_id);
        $content .= createBackLink ('Biomedical research: ' . $experiment_title,'nmv_list_victim_experiment?ID_experiment='.$experiment_id);
	}
    $content .= '</div>';

$layout
	->set('title','View Biomedical Research')
	->set('content',$content)
	->cast();
