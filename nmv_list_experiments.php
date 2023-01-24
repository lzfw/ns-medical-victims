<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');

$dbi->setUserVar ('sort',getUrlParameter('sort'),'experiment_title');
$dbi->setUserVar ('order',getUrlParameter('order'),'ASC');
$dbi->setUserVar ('skip',getUrlParameter('skip'),0);

$options = '';
if ($dbi->checkUserPermission('edit')) {
		$options .= createSmallButton(L_EDIT,'nmv_edit_experiment?ID_experiment={ID_experiment}','icon edit');
}
if ($dbi->checkUserPermission('admin')) {
		$options .= createSmallButton(L_DELETE,'nmv_remove_experiment?ID_experiment={ID_experiment}','icon delete');
}

// Select-Klauseln erstellen
$querystring_count = 'SELECT COUNT(ID_experiment) AS total FROM nmv__experiment v'; // für Treffer gesamt
$querystring_items = "SELECT e.ID_experiment, GROUP_CONCAT(DISTINCT i.institution_name SEPARATOR '\n') AS institutions , e.experiment_title, e.objective, c.english AS classification,
															e.start_year, e.end_year, GROUP_CONCAT(DISTINCT f.english ORDER BY f.english ASC SEPARATOR '\n') AS fields_of_interest
							        FROM nmv__experiment e
							        LEFT JOIN nmv__experiment_classification c 	ON c.ID_exp_classification = e.ID_exp_classification
											LEFT JOIN nmv__experiment_institution ei 		ON ei.ID_experiment = e.ID_experiment
											LEFT JOIN nmv__institution i 								ON i.ID_institution = ei.ID_institution
											LEFT JOIN nmv__experiment_foi ef						ON ef.ID_experiment = e.ID_experiment
											LEFT JOIN nmv__field_of_interest f 					ON f.ID_foi = ef.ID_foi
											GROUP BY e.ID_experiment"; // für Ergebnisliste
$querystring_where = array(); // für Filter

// Gesamtanzahl der Suchergebnisse feststellen
$query_count = $dbi->connection->query($querystring_count);
$total_results = $query_count->fetch_object();
$dbi->setUserVar('total_results',$total_results->total);

// order-klausel
$querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']}";

// query ausführen
$query_items = $dbi->connection->query($querystring_items.$querystring_orderby);
$layout
	->set('title','Biomedical Research')
	->set('content',
			($dbi->checkUserPermission('edit')
			? '<div class="buttons">'.createButton ('New Biomedical Research','nmv_edit_experiment','icon add').'</div>'
			: '')
			. '<div class="buttons">'.createButton ('Export Table to .csv','nmv_export.php?type=csv&entity=experiment&where-clause=','icon download')
															.createButton ('Export Table to .xls (Excel)','nmv_export.php?type=xls&entity=experiment&where-clause=','icon download')
			. '</div>'
			. '<p>Number of experiments: '. $total_results->total. ' </p>'
			. $dbi->getListView('nmv_experiments_table',$query_items)
	    . ($dbi->checkUserPermission('edit')
	        ? '<div class="buttons">'.createButton ('New Biomedical Research','nmv_edit_experiment','icon add').'</div>'
	        : '')
	    . createBackLink (L_CONTENTS,'z_menu_contents')
	)
	->cast();
