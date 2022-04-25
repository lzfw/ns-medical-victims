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
$querystring = "SELECT
                    ve.ID_victim AS ID_victim, ve.ID_experiment,
                    v.first_names AS first_names, v.surname AS surname, v.birth_place AS birth_place,
                    LEFT(concat(IFNULL(LEFT(e.experiment_title, 60), '#'),' - ',IFNULL(e.funding, '#')),100) AS experiment,
                    ve.experiment_duration AS experiment_duration, ve.age_experiment_start AS age_experiment_start,
                    ve.outcome_injuries AS outcome_injuries,
                    ve.notes_perpetrator AS notes_perpetrator, ve.narratives AS narratives,
                    CONCAT(IFNULL(ve.exp_start_day, '-'), '.',IFNULL(ve.exp_start_month, '-'), '.', IFNULL(ve.exp_start_year, '-')) AS ve_start_date,
                    CONCAT(IFNULL(ve.exp_end_day, '-'), '.',IFNULL(ve.exp_end_month, '-'), '.', IFNULL(ve.exp_end_year, '-')) AS ve_end_date,
                    ve.notes AS notes,
                    CONCAT(IFNULL(e.experiment_title, 'no entry'), ' - ID ',
                                  e.ID_experiment, ' - ',
                                  IFNULL(i.institution_name, 'no entry')) AS ei_info,
                    s.english AS survival
                FROM nmv__victim_experiment ve
                LEFT JOIN nmv__victim v                ON (ve.ID_victim = v.ID_victim)
                LEFT JOIN nmv__experiment e            ON (ve.ID_experiment = e.ID_experiment)
                LEFT JOIN nmv__institution i           ON (e.ID_institution = i.ID_institution)
                LEFT JOIN nmv__survival s              ON (ve.ID_survival = s.ID_survival)
                WHERE ve.ID_vict_exp = " . $dbi->getUserVar('ID_vict_exp');
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
    $content .= '<tr><th>Biomedical Research <br> (title, ID, institution)</th><td><a href="nmv_view_experiment?ID_experiment='. $experiment_id . '">' .
        htmlspecialchars($ei_info, ENT_HTML5).'</a></td></tr>';
    $content .= '<tr><th>Biomedical Research Duration</th><td>'.
        htmlspecialchars($ve->experiment_duration, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>Start and End Date D.M.Y</th><td>'.
        htmlspecialchars('from ' . $ve->ve_start_date . ' until ' . $ve->ve_end_date, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>Age (experiment start)</th><td>'.
        htmlspecialchars($ve->age_experiment_start, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>Injuries</th><td>'.
        htmlspecialchars($ve->outcome_injuries, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>Survival</th><td>'.
        htmlspecialchars($ve->survival, ENT_HTML5).'</td></tr>';
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
	->set('title','View Victim-Experiment-Link')
	->set('content',$content)
	->cast();
