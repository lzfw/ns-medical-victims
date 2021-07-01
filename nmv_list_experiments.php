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
$querystring_items = 'SELECT `ID_experiment`, i.institution_name, `experiment_title`, `field_of_interest`, `objective`, c.`english` classification
        FROM nmv__experiment e
        LEFT JOIN nmv__experiment_classification c 	ON c.`ID_exp_classification` = e.`classification`
				LEFT JOIN nmv__institution i 								ON i.ID_institution = e.ID_institution'; // für Ergebnisliste
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
			'<p>Number of experiments: '. $total_results->total. ' </p>'.
	    $dbi->getListView('nmv_experiments_table',$query_items)
	    .($dbi->checkUserPermission('edit')
	        ? '<div class="buttons">'.createButton ('New Biomedical Research','nmv_edit_experiment','icon add').'</div>'
	        : '')
	    .createBackLink (L_CONTENTS,'z_menu_contents')
	)
	->cast();
