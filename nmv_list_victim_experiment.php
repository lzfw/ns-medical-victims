<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');

// get ID_victim / _experiment
$victim_id = (int) getUrlParameter('ID_victim', 0);
$experiment_id = (int) getUrlParameter('ID_experiment', 0);

$victim_name = 'Error: Missing victim.';
$experiment_name = 'Error: Missing biomedical research.';
$title = '';
$content = '';

 //create a table of experiments a certain victim was involved in
if ($victim_id) {
    $dbi->addBreadcrumb ('Victims','nmv_list_victims');

    // query: get victim data
    $querystring = "
    SELECT CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_names, '')) victim_name
    FROM nmv__victim
    WHERE ID_victim = $victim_id";
    $query = $dbi->connection->query($querystring);
    $victim = $query->fetch_object();

    if ($victim) {
        $victim_name = $victim->victim_name;
        $title = 'Biomedical Research: ' . $victim_name;

        $dbi->addBreadcrumb ($victim_name,'nmv_view_victim?ID_victim='.$victim_id);

        //get number of experiments
        $querystring_count = "
        SELECT COUNT(ve.ID_experiment) AS total
          FROM nmv__victim_experiment ve
          LEFT JOIN nmv__experiment e ON e.ID_experiment = ve.ID_experiment
          LEFT JOIN nmv__victim v ON v.ID_victim = ve.ID_victim
          LEFT JOIN nmv__experiment_classification c on c.ID_exp_classification = e.classification
          WHERE ve.ID_victim = $victim_id";
        $query_count = $dbi->connection->query($querystring_count);
        $total_results = $query_count->fetch_object();
        $experiment_count = $total_results->total;


        // query: get experiment data
        $querystring = "
        SELECT ve.ID_vict_exp ID_vict_exp, ve.experiment_duration duration, ve.age_experiment_start age,
            COALESCE(e.experiment_title, 'unspecified') title, c.english classification,
            ve.ID_experiment ID_experiment
        FROM nmv__victim_experiment ve
        LEFT JOIN nmv__experiment e ON e.ID_experiment = ve.ID_experiment
        LEFT JOIN nmv__victim v ON v.ID_victim = ve.ID_victim
        LEFT JOIN nmv__experiment_classification c on c.ID_exp_classification = e.classification
        WHERE ve.ID_victim = $victim_id
        ORDER BY exp_start_year, exp_start_month, exp_start_day, exp_end_year, exp_end_month, exp_end_day
        LIMIT 300";

        $options = '';
        $row_template = ['{title}', '{classification}', '{duration}', '{age}'];
        $header_template = ['Title', 'Classification', 'Duration', 'Age'];

        $options .= createSmallButton('View Research','nmv_view_victim_experiment?ID_vict_exp={ID_vict_exp}','icon view');
        if ($dbi->checkUserPermission('edit') || $dbi->checkUserPermission('admin')) {
        	if ($dbi->checkUserPermission('edit')) {
        			$options .= createSmallButton(L_EDIT,'nmv_edit_victim_experiment?ID_vict_exp={ID_vict_exp}','icon edit');
        	}
        	if ($dbi->checkUserPermission('admin')) {
        			$options .= createSmallButton(L_DELETE,'nmv_remove_victim_experiment?ID_vict_exp={ID_vict_exp}','icon delete');
        	}
        }
    	$row_template[] = $options;
    	$header_template[] = L_OPTIONS;

        $content .= '<p>Number of experiments: '.$experiment_count.'</p>';
        $content .= buildTableFromQuery(
            $querystring,
            $row_template,
            $header_template,
            'grid');

        if ($dbi->checkUserPermission('edit')) {
        	$content .= '<div class="buttons">';
        	$content .= createButton ('New Biomedical Research Entry',
        	    'nmv_edit_victim_experiment?ID_victim='.$victim_id,'icon add');
        	$content .= '</div>';
        }
    }

    $content .= createBackLink ('View victim: '.$victim_name,'nmv_view_victim?ID_victim='.$victim_id);
}


//create a table of victims of a certain experiment
if ($experiment_id) {
    // FIXME: TODO: Sad as it is, needs pagination
    $dbi->addBreadcrumb ('Biomedical research','nmv_list_experiments');

    // query: get experiment data
    $querystring = "
    SELECT CONCAT(COALESCE(experiment_title, ''), ' - ', COALESCE(field_of_interest, '')) experiment_name
    FROM nmv__experiment
    WHERE ID_experiment = $experiment_id";
    $query = $dbi->connection->query($querystring);
    $experiment = $query->fetch_object();

    if ($experiment) {
        $experiment_name = $experiment->experiment_name;
        $title = 'Victims List: ' . $experiment_name;

        $dbi->addBreadcrumb ($experiment_name,'nmv_view_experiment?ID_experiment='.$experiment_id);

        //get number of victims
        $querystring_count = "
        SELECT COUNT(ve.ID_victim) AS total
          FROM nmv__victim_experiment ve
          LEFT JOIN nmv__experiment e ON e.ID_experiment = ve.ID_experiment
          LEFT JOIN nmv__victim v ON v.ID_victim = ve.ID_victim
          LEFT JOIN nmv__experiment_classification c on c.ID_exp_classification = e.classification
          WHERE ve.ID_experiment = $experiment_id";
        $query_count = $dbi->connection->query($querystring_count);
        $total_results = $query_count->fetch_object();
        $victim_count = $total_results->total;


        // query: get data of the victims of the experiment
        $querystring = "
        SELECT ve.ID_vict_exp ID_vict_exp,
            CONCAT(v.first_names, ' ', v.surname) victim_name,
            v.birth_country birth_country, v.birth_place birth_place,
            CONCAT_WS('-', v.birth_year, v.birth_month, v.birth_day) birth_date,
            ve.ID_victim ID_victim
        FROM nmv__victim_experiment ve
        LEFT JOIN nmv__experiment e ON e.ID_experiment = ve.ID_experiment
        LEFT JOIN nmv__victim v ON v.ID_victim = ve.ID_victim
        LEFT JOIN nmv__experiment_classification c on c.ID_exp_classification = e.classification
        WHERE ve.ID_experiment = $experiment_id
        ORDER BY exp_start_year, exp_start_month, exp_start_day, exp_end_year, exp_end_month, exp_end_day
        LIMIT 600";

        $options = '';
        $row_template = ['{victim_name}', '{birth_country}', '{birth_place}', '{birth_date}'];
        $header_template = ['Victim', 'Country of Birth', 'Birth Place', 'Birth Date'];

        $options .= createSmallButton('View Details','nmv_view_victim_experiment?ID_vict_exp={ID_vict_exp}','icon view');
        if ($dbi->checkUserPermission('edit') || $dbi->checkUserPermission('admin')) {
        	if ($dbi->checkUserPermission('edit')) {
        			$options .= createSmallButton(L_EDIT,'nmv_edit_victim_experiment?ID_vict_exp={ID_vict_exp}','icon edit');
        	}
        	if ($dbi->checkUserPermission('admin')) {
        			$options .= createSmallButton(L_DELETE,'nmv_remove_victim_experiment?ID_vict_exp={ID_vict_exp}','icon delete');
        	}
        }
    	$row_template[] = $options;
    	$header_template[] = L_OPTIONS;

        $content .= '<p>Number of victims: '.$victim_count.'</p>';
        $content .= buildTableFromQuery(
            $querystring,
            $row_template,
            $header_template,
            'grid');

        // Disabled for now - needs support by nmv_edit_victim_experiment
        /*
        if ($dbi->checkUserPermission('edit')) {
        	$content .= '<div class="buttons">';
        	$content .= createButton ('New Biomedical Research Entry',
        	    'nmv_edit_victim_experiment?ID_experiment='.$experiment_id,'icon add');
        	$content .= '</div>';
        }*/
    }

    $content .= createBackLink ('View Biomedical Research: '.$experiment_name,'nmv_view_experiment?ID_experiment='.$experiment_id);
}

$layout
	->set('title', $title)
	->set('content',$content)
	->cast();
