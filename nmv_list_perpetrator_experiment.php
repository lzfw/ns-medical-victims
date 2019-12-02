<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');

$perpetrator_id = (int) getUrlParameter('ID_perpetrator', 0);
$experiment_id = (int) getUrlParameter('ID_experiment', 0);

$perpetrator_name = 'Error: Missing perpetrator.';
$experiment_name = 'Error: Missing biomedical research.';
$content = '';

if ($perpetrator_id) {
    $dbi->addBreadcrumb ('Perpetrators','nmv_list_perpetrators');

    // query: get perpetrator data
    $querystring = "
    SELECT CONCAT(COALESCE(type_of_qualification, ''), ' ', COALESCE(surname, ''), ' ', COALESCE(first_names, '')) perpetrator_name
    FROM nmv__perpetrator
    WHERE ID_perpetrator = $perpetrator_id";
    $query = $dbi->connection->query($querystring);
    $perpetrator = $query->fetch_object();

    if ($perpetrator) {
        $perpetrator_name = $perpetrator->perpetrator_name;

        $dbi->addBreadcrumb ($perpetrator_name,'nmv_view_perpetrator?ID_perpetrator='.$perpetrator_id);

        // query: get hosp data
        $querystring = "
        SELECT pe.ID_perp_exp ID_perp_exp,
            COALESCE(e.experiment_title, 'unspecified') title, c.english classification,
            pe.ID_experiment ID_experiment
        FROM nmv__perpetrator_experiment pe
        LEFT JOIN nmv__experiment e ON e.ID_experiment = pe.ID_experiment
        LEFT JOIN nmv__perpetrator p ON p.ID_perpetrator = pe.ID_perpetrator
        LEFT JOIN nmv__experiment_classification c on c.ID_exp_classification = e.classification
        WHERE pe.ID_perpetrator = $perpetrator_id
        ORDER BY title
        LIMIT 300";

        $options = '';
        $row_template = ['{title}', '{classification}'];
        $header_template = ['Title', 'Classification'];

        $options .= createSmallButton('View Biomedical Research','nmv_view_experiment?ID_experiment={ID_experiment}','icon view');
        if ($dbi->checkUserPermission('edit') || $dbi->checkUserPermission('admin')) {
        	if ($dbi->checkUserPermission('edit')) {
        			$options .= createSmallButton(L_EDIT,'nmv_edit_perpetrator_experiment?ID_perp_exp={ID_perp_exp}','icon edit');
        	}
        	if ($dbi->checkUserPermission('admin')) {
        			$options .= createSmallButton(L_DELETE,'nmv_remove_perpetrator_experiment?ID_perp_exp={ID_perp_exp}','icon delete');
        	}
        }
    	$row_template[] = $options;
    	$header_template[] = L_OPTIONS;

        $content .= buildTableFromQuery(
            $querystring,
            $row_template,
            $header_template,
            'grid');

        if ($dbi->checkUserPermission('edit')) {
        	$content .= '<div class="buttons">';
        	$content .= createButton ('New Biomedical Research Entry',
        	    'nmv_edit_perpetrator_experiment?ID_perpetrator='.$perpetrator_id,'icon add');
        	$content .= '</div>';
        }
    }

    $content .= createBackLink ('View perpetrator: '.$perpetrator_name,'nmv_view_perpetrator?ID_perpetrator='.$perpetrator_id);
}

if ($experiment_id) {
    $dbi->addBreadcrumb ('Biomedical Research','nmv_list_experiments');

    // query: get experiment data
    $querystring = "
    SELECT CONCAT(COALESCE(experiment_title, ''), ' - ', COALESCE(field_of_interest, '')) experiment_name
    FROM nmv__experiment
    WHERE ID_experiment = $experiment_id";
    $query = $dbi->connection->query($querystring);
    $experiment = $query->fetch_object();

    if ($experiment) {
        $experiment_name = $experiment->experiment_name;

        $dbi->addBreadcrumb ($experiment_name,'nmv_view_experiment?ID_experiment='.$experiment_id);

        // query: get hosp data
        $querystring = "
        SELECT pe.ID_perp_exp ID_perp_exp, CONCAT(p.first_names, ' ', p.surname) perpetrator_name,
            p.birth_country birth_country, p.birth_place birth_place,
            CONCAT_WS('-', p.birth_year, p.birth_month, p.birth_day) birth_date,
            pe.ID_perpetrator ID_perpetrator
        FROM nmv__perpetrator_experiment pe
        LEFT JOIN nmv__experiment e ON e.ID_experiment = pe.ID_experiment
        LEFT JOIN nmv__perpetrator p ON p.ID_perpetrator = pe.ID_perpetrator
        LEFT JOIN nmv__experiment_classification c on c.ID_exp_classification = e.classification
        WHERE pe.ID_experiment = $experiment_id
        ORDER BY perpetrator_name
        LIMIT 300";

        $options = '';
        $row_template = ['{perpetrator_name}', '{birth_country}', '{birth_place}', '{birth_date}'];
        $header_template = ['Perpetrator', 'Country of Birth', 'Birth Place', 'Birth Date'];

        $options .= createSmallButton('View Perpetrator','nmv_view_perpetrator?ID_perpetrator={ID_perpetrator}','icon view');
        if ($dbi->checkUserPermission('edit') || $dbi->checkUserPermission('admin')) {
        	if ($dbi->checkUserPermission('edit')) {
        			$options .= createSmallButton(L_EDIT,'nmv_edit_perpetrator_experiment?ID_perp_exp={ID_perp_exp}','icon edit');
        	}
        	if ($dbi->checkUserPermission('admin')) {
        			$options .= createSmallButton(L_DELETE,'nmv_remove_perpetrator_experiment?ID_perp_exp={ID_perp_exp}','icon delete');
        	}
        }
    	$row_template[] = $options;
    	$header_template[] = L_OPTIONS;

        $content .= buildTableFromQuery(
            $querystring,
            $row_template,
            $header_template,
            'grid');

        // Not supported by nmv_edit_perpetrator_experiment yet
        /*
        if ($dbi->checkUserPermission('edit')) {
        	$content .= '<div class="buttons">';
        	$content .= createButton ('New Biomedical Research Entry',
        	    'nmv_edit_perpetrator_experiment?ID_experiment='.$experiment_id,'icon add');
        	$content .= '</div>';
        }*/
    }

    $content .= createBackLink ('View Biomedical Research: '.$experiment_name,'nmv_view_experiment?ID_experiment='.$experiment_id);
}

$layout
	->set('title',($perpetrator_id ? 'Biomedical Research: "' . $perpetrator_name . '"' : 'Perpetrators List: "' . $experiment_name . '"'))
	->set('content',$content)
	->cast();