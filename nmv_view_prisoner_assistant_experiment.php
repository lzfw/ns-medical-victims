<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$dbi->setUserVar ('ID_pa_exp',getUrlParameter('ID_pa_exp'),NULL);
$pa_exp_id = (int) getUrlParameter('ID_pa_exp',0);
$victim_id = 0;
$victim_name = 'Error: Unknown victim';
$experiment_title = 'Error: Unknown biomedical research';
$experiment_id = 0;

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Prisoner Assistants','nmv_list_prisoner_assistants');



// query: get prisoner assistant - experiment data
$querystring = "SELECT
                    pae.ID_victim AS ID_victim, pae.ID_experiment,
                    pa.first_names AS first_names, pa.surname AS surname,
                    LEFT(concat(IFNULL(LEFT(e.experiment_title, 60), '#'),' - ',IFNULL(e.funding, '#')),100) AS experiment,
                    pae.narratives AS narratives,
                    CONCAT(IFNULL(pae.exp_start_day, '-'), '.',IFNULL(pae.exp_start_month, '-'), '.', IFNULL(pae.exp_start_year, '-')) AS pae_start_date,
                    CONCAT(IFNULL(pae.exp_end_day, '-'), '.',IFNULL(pae.exp_end_month, '-'), '.', IFNULL(pae.exp_end_year, '-')) AS pae_end_date,
                    pae.notes_about_involvement AS notes_about_involvement,
                    CONCAT(IFNULL(e.experiment_title, 'no entry'), ' - ID ',
                                  e.ID_experiment, ' - ',
                                  IFNULL(GROUP_CONCAT(i.institution_name SEPARATOR ' and '), 'no entry')
                                  ) AS ei_info,
                    ro.role, pae.role_other, pae.gave_testimony_in_trial
                FROM nmv__prisoner_assistant_experiment pae
                LEFT JOIN nmv__victim pa                ON (pae.ID_victim = pa.ID_victim)
                LEFT JOIN nmv__experiment e             ON (pae.ID_experiment = e.ID_experiment)
                LEFT JOIN nmv__experiment_institution ei    ON ei.ID_experiment = e.ID_experiment
                LEFT JOIN nmv__institution i                ON i.ID_institution = ei.ID_institution
                LEFT JOIN nmv__role ro                  ON (ro.ID_role = pae.ID_role)
                WHERE pae.ID_pa_exp = " . $dbi->getUserVar('ID_pa_exp') . "
                GROUP BY ID_experiment";
$query = $dbi->connection->query($querystring);

$content = '';
$content .= '<br><table class="grid">';
if ($pae = $query->fetch_object()) {
    $victim_id = $pae->ID_victim;
    $victim_name = $pae->first_names . ' ' . $pae->surname;
    $experiment_title = $pae->experiment;
    $experiment_id = $pae->ID_experiment;
    $ei_info = $pae->ei_info;

    $dbi->addBreadcrumb ($victim_name,'nmv_view_victim?ID_victim=' . $victim_id);
    $dbi->addBreadcrumb ('Biomedical Research','nmv_list_victim_experiment?ID_victim=' . $victim_id);


    $content .= '<tr><th>ID Prisoner_Assistant - Experiment</th><td>'.
        htmlspecialchars((string) $pa_exp_id, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>ID Person</th><td>'.
        htmlspecialchars((string) $victim_id, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>prisoner assistant</th><td><a href="nmv_view_victim?ID_victim='. $victim_id . '">' .
        htmlspecialchars((string) $victim_name, ENT_HTML5).'</a></td></tr>';
    $content .= '<tr><th>biomedical research <br> (title, ID, institution)</th><td><a href="nmv_view_experiment?ID_experiment='. $experiment_id . '">' .
        htmlspecialchars((string) $ei_info, ENT_HTML5).'</a></td></tr>';
    $content .= '<tr><th>start and end date D.M.Y<br> (of involvement)</th><td>'.
        htmlspecialchars('from ' . $pae->pae_start_date . ' until ' . $pae->pae_end_date, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>role in experiment</th><td>'.
        htmlspecialchars((string) $pae->role, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>other role in experiment<br>(if role not found in selection)</th><td>'.
        htmlspecialchars((string) $pae->role_other, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>gave testimony in trial</th><td>'.
        htmlspecialchars((string) $pae->gave_testimony_in_trial, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>subjective narratives</th><td>'.
        htmlspecialchars((string) $pae->narratives, ENT_HTML5).'</td></tr>';
    $content .= '<tr><th>notes about involment</th><td>'.
        htmlspecialchars((string) $pae->notes_about_involvement, ENT_HTML5).'</td></tr>';
}
$content .= '</table>';

	$content .= '<div class="buttons">';
	if ($dbi->checkUserPermission('edit'))
	    $content .= createButton ('Edit','nmv_edit_prisoner_assistant_experiment?ID_pa_exp='.$pa_exp_id,'icon edit');
	if ($dbi->checkUserPermission('admin'))
	    $content .= createButton(L_DELETE,'nmv_remove_prisoner_assistant_experiment?ID_pa_exp='.$pa_exp_id,'icon delete');
  $content .= '</div><br>';

  if ($victim_id) {
        $content .= createBackLink ('Biomedical research list: ' . $victim_name,'nmv_list_victim_experiment?ID_victim='.$victim_id);
        $content .= createBackLink ('Prisoner Assistant list: Experiment ' . $experiment_title,'nmv_list_prisoner_assistant_experiment?ID_experiment='.$experiment_id);
  }

$layout
	->set('title','View Prisoner_Assistant-Experiment-Link')
	->set('content',$content)
	->cast();
