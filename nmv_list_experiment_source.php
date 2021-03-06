<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');

$source_id = (int) getUrlParameter('ID_source', 0);
$experiment_id = (int) getUrlParameter('ID_experiment', 0);

$source_name = 'Error: Missing source.';
$experiment_name = 'Error: Missing experiment.';
$content = '';

if ($source_id) {
    $dbi->addBreadcrumb ('Source','nmv_list_source');

    // query: get source data
    $querystring = "
    SELECT CONCAT(COALESCE(source_title, ''), ' ', COALESCE(creation_year, '')) source_name
    FROM nmv__source
    WHERE ID_source = $source_id";
    $query = $dbi->connection->query($querystring);
    $source = $query->fetch_object();

    if ($source) {
        $source_name = $source->source_name;

        $dbi->addBreadcrumb ($source_name,'nmv_view_source?ID_source='.$source_id);

        // query: get hosp data
        $querystring = "
        SELECT es.ID_exp_source ID_exp_source, es.location location, e.ID_experiment ID_experiment,
            COALESCE(e.experiment_title, 'unspecified') title, c.english classification
        FROM nmv__experiment_source es
        LEFT JOIN nmv__experiment e ON e.ID_experiment = es.ID_experiment
        LEFT JOIN nmv__source s ON s.ID_source = es.ID_source
        LEFT JOIN nmv__experiment_classification c on c.ID_exp_classification = e.classification
        WHERE es.ID_source = $source_id
        ORDER BY title
        LIMIT 300";

        $options = '';
        $row_template = ['<a href="nmv_view_experiment?ID_experiment={ID_experiment}">{title}</a>', '{classification}', '{location}'];
        $header_template = ['Title', 'Classification', 'Location'];

        $options .= createSmallButton('view Experiment','nmv_view_experiment?ID_experiment={ID_experiment}','icon view');
        if ($dbi->checkUserPermission('edit') || $dbi->checkUserPermission('admin')) {
        	if ($dbi->checkUserPermission('edit')) {
        			$options .= createSmallButton(L_EDIT,'nmv_edit_experiment_source?ID_exp_source={ID_exp_source}','icon edit');
        	}
        	if ($dbi->checkUserPermission('admin')) {
        			$options .= createSmallButton(L_DELETE,'nmv_remove_experiment_source?ID_exp_source={ID_exp_source}','icon delete');
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
        	    'nmv_edit_experiment_source?ID_source='.$source_id,'icon add');
        	$content .= '</div>';
        }
    }

    $content .= createBackLink ('View source: '.$source_name,'nmv_view_source?ID_source='.$source_id);
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
        SELECT es.ID_exp_source ID_exp_source, s.source_title title, s.ID_source ID_source,
        s.medium medium, s.creation_year year, es.location location
        FROM nmv__experiment_source es
        LEFT JOIN nmv__experiment e ON e.ID_experiment = es.ID_experiment
        LEFT JOIN nmv__source s ON s.ID_source = es.ID_source
        LEFT JOIN nmv__experiment_classification c on c.ID_exp_classification = e.classification
        WHERE es.ID_experiment = $experiment_id
        ORDER BY title
        LIMIT 300";

        $options = '';
        $row_template = ['<a href="nmv_view_source?ID_source={ID_source}">{title}</a>', '{medium}', '{year}', '{location}'];
        $header_template = ['Title', 'Medium', 'Year', 'Location'];

        if ($dbi->checkUserPermission('edit') || $dbi->checkUserPermission('admin')) {
        	if ($dbi->checkUserPermission('edit')) {
        			$options .= createSmallButton(L_EDIT,'nmv_edit_experiment_source?ID_exp_source={ID_exp_source}','icon edit');
        	}
        	if ($dbi->checkUserPermission('admin')) {
        			$options .= createSmallButton(L_DELETE,'nmv_remove_experiment_source?ID_exp_source={ID_exp_source}','icon delete');
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
        	$content .= createButton ('New Source Entry',
        	    'nmv_edit_experiment_source?ID_experiment='.$experiment_id,'icon add');
        	$content .= '</div>';
        }
    }

    $content .= createBackLink ('View Biomedical Research: '.$experiment_name,'nmv_view_experiment?ID_experiment='.$experiment_id);
}

$layout
	->set('title',($source_id ? 'Biomedical Research: "' . $source_name . '"' : 'Sources List: "' . $experiment_name . '"'))
	->set('content',$content)
	->cast();
