<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');

$literature_id = (int) getUrlParameter('ID_literature', 0);
$experiment_id = (int) getUrlParameter('ID_experiment', 0);

$literature_name = 'Error: Missing literature.';
$experiment_name = 'Error: Missing biomedical research.';
$content = '';

if ($literature_id) {
    $dbi->addBreadcrumb ('Literature','nmv_list_literature');

    // query: get literature data
    $querystring = "
    SELECT CONCAT(COALESCE(lit_title, ''), ' ', COALESCE(authors, '')) literature_name
    FROM nmv__literature
    WHERE ID_literature = $literature_id";
    $query = $dbi->connection->query($querystring);
    $literature = $query->fetch_object();

    if ($literature) {
        $literature_name = $literature->literature_name;

        $dbi->addBreadcrumb ($literature_name,'nmv_view_literature?ID_literature='.$literature_id);

        // query: get hosp data
        $querystring = "
        SELECT el.ID_exp_lit ID_exp_lit, el.pages pages, el.ID_experiment ID_experiment,
            COALESCE(e.experiment_title, 'unspecified') title, c.classification,
                        el.url, CONCAT(IFNULL(el.access_day, '-'), '.', IFNULL(el.access_month, '-'), '.', IFNULL(el.access_year, '-')) as access_date
        FROM nmv__experiment_literature el
        LEFT JOIN nmv__experiment e ON e.ID_experiment = el.ID_experiment
        LEFT JOIN nmv__literature l ON l.ID_literature = el.ID_literature
        LEFT JOIN nmv__experiment_classification c on c.ID_exp_classification = e.ID_exp_classification
        WHERE el.ID_literature = $literature_id
        ORDER BY title";

        $querystring_count = "SELECT COUNT(*) AS total FROM nmv__experiment_literature el WHERE el.ID_literature = $literature_id"; // für Treffer gesamt

        // Gesamtanzahl der Suchergebnisse feststellen
        $query_count = $dbi->connection->query($querystring_count);
        $total_results = $query_count->fetch_object();

        $options = '';
        $row_template = ['<a href="nmv_view_experiment?ID_experiment={ID_experiment}">{title}</a>', '{classification}', '{pages}', '{url}', '{access_date}'];
        $header_template = ['Title', 'Classification', 'Pages', 'URL', 'Access Date'];

        $options .= createSmallButton('view Experiment','nmv_view_experiment?ID_experiment={ID_experiment}','icon view');
        if ($dbi->checkUserPermission('edit') || $dbi->checkUserPermission('admin')) {
        	if ($dbi->checkUserPermission('edit')) {
        			$options .= createSmallButton(L_EDIT,'nmv_edit_experiment_literature?ID_exp_lit={ID_exp_lit}','icon edit');
        	}
        	if ($dbi->checkUserPermission('admin')) {
        			$options .= createSmallButton(L_DELETE,'nmv_remove_experiment_literature?ID_exp_lit={ID_exp_lit}','icon delete');
        	}
        }
        $row_template[] = $options;
        $header_template[] = L_OPTIONS;

        $content .= '<p>Number of experiments: ' . $total_results->total . ' </p>';

        // new entry button
        if ($dbi->checkUserPermission('edit')) {
        	$content .= '<div class="buttons">';
        	$content .= createButton ('New Biomedical Research Entry',
        	    'nmv_edit_experiment_literature?ID_literature='.$literature_id,'icon add');
        	$content .= '</div>';
        }

        // table
        $content .= buildTableFromQuery(
            $querystring,
            $row_template,
            $header_template,
            'grid');

        // new entry button
        if ($dbi->checkUserPermission('edit')) {
        	$content .= '<div class="buttons">';
        	$content .= createButton ('New Biomedical Research Entry',
        	    'nmv_edit_experiment_literature?ID_literature='.$literature_id,'icon add');
        	$content .= '</div>';
        }
    }

    $content .= createBackLink ('View literature: '.$literature_name,'nmv_view_literature?ID_literature='.$literature_id);
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
        SELECT el.ID_exp_lit ID_exp_lit, l.lit_title title, l.authors authors,
            el.pages pages, el.ID_literature ID_literature,
            el.url, CONCAT(IFNULL(el.access_day, '-'), '.', IFNULL(el.access_month, '-'), '.', IFNULL(el.access_year, '-')) as access_date
        FROM nmv__experiment_literature el
        LEFT JOIN nmv__experiment e ON e.ID_experiment = el.ID_experiment
        LEFT JOIN nmv__literature l ON l.ID_literature = el.ID_literature
        LEFT JOIN nmv__experiment_classification c on c.ID_exp_classification = e.ID_exp_classification
        WHERE el.ID_experiment = $experiment_id
        ORDER BY title";

        $querystring_count = "SELECT COUNT(*) AS total FROM nmv__experiment_literature el WHERE el.ID_experiment = $experiment_id"; // für Treffer gesamt

        // Gesamtanzahl der Suchergebnisse feststellen
        $query_count = $dbi->connection->query($querystring_count);
        $total_results = $query_count->fetch_object();

        $options = '';
        $row_template = ['<a href="nmv_view_literature?ID_literature={ID_literature}">{title}</a>', '{authors}', '{pages}', '{url}', '{access_date}'];
        $header_template = ['Title', 'Authors', 'Pages', 'URL', 'Access Date'];

        if ($dbi->checkUserPermission('edit') || $dbi->checkUserPermission('admin')) {
        	if ($dbi->checkUserPermission('edit')) {
        			$options .= createSmallButton(L_EDIT,'nmv_edit_experiment_literature?ID_exp_lit={ID_exp_lit}','icon edit');
        	}
        	if ($dbi->checkUserPermission('admin')) {
        			$options .= createSmallButton(L_DELETE,'nmv_remove_experiment_literature?ID_exp_lit={ID_exp_lit}','icon delete');
        	}
        	$row_template[] = $options;
        	$header_template[] = L_OPTIONS;
        }

        $content .= '<p>Number of literature: ' . $total_results->total . ' </p>';

        // new entry - button
        if ($dbi->checkUserPermission('edit')) {
        	$content .= '<div class="buttons">';
        	$content .= createButton ('New Literature Entry',
        	    'nmv_edit_experiment_literature?ID_experiment='.$experiment_id,'icon add');
        	$content .= '</div>';
        }

        // table view
        $content .= buildTableFromQuery(
            $querystring,
            $row_template,
            $header_template,
            'grid');

        // new entry - button
        if ($dbi->checkUserPermission('edit')) {
        	$content .= '<div class="buttons">';
        	$content .= createButton ('New Literature Entry',
        	    'nmv_edit_experiment_literature?ID_experiment='.$experiment_id,'icon add');
        	$content .= '</div>';
        }
    }

    $content .= createBackLink ('View Biomedical Research: '.$experiment_name,'nmv_view_experiment?ID_experiment='.$experiment_id);
}

$layout
	->set('title',($literature_id ? 'Biomedical Research: "' . $literature_name . '"' : 'Literature List: "' . $experiment_name . '"'))
	->set('content',$content)
	->cast();
