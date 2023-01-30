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
            CONCAT(COALESCE(e.experiment_title, 'unspecified'), '<br>institution: ',
            COALESCE(GROUP_CONCAT(i.institution_name SEPARATOR ';\n '), '-')) title, c.classification
        FROM nmv__experiment_source es
        LEFT JOIN nmv__experiment e                 ON e.ID_experiment = es.ID_experiment
        LEFT JOIN nmv__source s                     ON s.ID_source = es.ID_source
        LEFT JOIN nmv__experiment_classification c  ON c.ID_exp_classification = e.ID_exp_classification
        LEFT JOIN nmv__experiment_institution ei    ON ei.ID_experiment = es.ID_experiment
        LEFT JOIN nmv__institution i                ON i.ID_institution = ei.ID_institution
        WHERE es.ID_source = $source_id
        GROUP BY es.ID_exp_source
        ORDER BY title";

        $querystring_count = "SELECT COUNT(*) AS total FROM nmv__experiment_source es WHERE es.ID_source = $source_id"; // für Treffer gesamt

        // Gesamtanzahl der Suchergebnisse feststellen
        $query_count = $dbi->connection->query($querystring_count);
        $total_results = $query_count->fetch_object();

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


        $content .= '<p>Number of experiments: ' . $total_results->total . ' </p>';

        //new experiment - button
        if ($dbi->checkUserPermission('edit')) {
          $content .= '<div class="buttons">';
          $content .= createButton ('New Biomedical Research Entry',
              'nmv_edit_experiment_source?ID_source='.$source_id,'icon add');
          $content .= '</div>';
        }

        // table view
        $content .= buildTableFromQuery(
            $querystring,
            $row_template,
            $header_template,
            'grid');

        // new experiment button
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
      SELECT CONCAT(COALESCE(experiment_title, '')) experiment_name
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
        s.medium medium, s.creation_year year, es.location location,
        es.url, CONCAT(IFNULL(es.access_day, '-'), '.', IFNULL(es.access_month, '-'), '.', IFNULL(es.access_year, '-')) as access_date
        FROM nmv__experiment_source es
        LEFT JOIN nmv__experiment e ON e.ID_experiment = es.ID_experiment
        LEFT JOIN nmv__source s ON s.ID_source = es.ID_source
        LEFT JOIN nmv__experiment_classification c on c.ID_exp_classification = e.ID_exp_classification
        WHERE es.ID_experiment = $experiment_id
        ORDER BY title";

        $querystring_count = "SELECT COUNT(*) AS total FROM nmv__experiment_source es WHERE es.ID_experiment = $experiment_id"; // für Treffer gesamt

        // Gesamtanzahl der Suchergebnisse feststellen
        $query_count = $dbi->connection->query($querystring_count);
        $total_results = $query_count->fetch_object();


        $options = '';
        $row_template = ['<a href="nmv_view_source?ID_source={ID_source}">{title}</a>', '{medium}', '{year}', '{location}', '{url}', '{access_date}'];
        $header_template = ['Title', 'Medium', 'Year', 'Location', 'URL', 'Access date'];

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

        $content .= '<p>Number of sources: ' . $total_results->total . ' </p>';

        // new source - button
        if ($dbi->checkUserPermission('edit')) {
          $content .= '<div class="buttons">';
          $content .= createButton ('New Source Entry',
              'nmv_edit_experiment_source?ID_experiment='.$experiment_id,'icon add');
          $content .= '</div>';
        }

        // table view
        $content .= buildTableFromQuery(
            $querystring,
            $row_template,
            $header_template,
            'grid');

        // new source - button
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
