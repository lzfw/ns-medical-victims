<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');

// browsing options
$dbi->setUserVar ('sort',getUrlParameter('sort'),'institution_name');
$dbi->setUserVar ('order',getUrlParameter('order'),'ASC');
$dbi->setUserVar ('skip',getUrlParameter('skip'),0);

$options = '';
if ($dbi->checkUserPermission('edit')) {
		$options .= createSmallButton(L_EDIT,'nmv_edit_institution?ID_institution={ID_institution}','icon edit');
}
if ($dbi->checkUserPermission('admin')) {
		$options .= createSmallButton(L_DELETE,'nmv_remove_institution?ID_institution={ID_institution}','icon delete');
}

// Select-Klauseln erstellen
$querystring_count = 'SELECT COUNT(*) AS total FROM nmv__institution i'; // für Treffer gesamt
$querystring_items = "SELECT ID_institution, IFNULL(institution_name, 'unavailable') institution_name,
 location, c.english AS country, it.english AS itype, notes
FROM nmv__institution i
LEFT JOIN nmv__institution_type it ON i.ID_institution_type = it.ID_institution_type
LEFT JOIN nmv__country c ON c.ID_country = i.ID_country"; // für Ergebnisliste
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
	->set('title','Institutions')
	->set('content',
			'<p>Number of institution entries: ' . $total_results->total . ' </p>'
			. ($dbi->checkUserPermission('edit')
	        ? '<div class="buttons">'.createButton ('New Institution','nmv_edit_institution','icon addUser').'</div>'
	        : '')
			. '<div class="buttons">'.createButton ('Export Table to .csv','nmv_export.php?type=csv&entity=institution&where-clause=','icon download')
																.createButton ('Export Table to .xls','nmv_export.php?type=xls&entity=institution&where-clause=','icon download')
			.'</div>'
	    . $dbi->getListView('nmv_institutions_table',$query_items)
	    . ($dbi->checkUserPermission('edit')
	        ? '<div class="buttons">'.createButton ('New Institution','nmv_edit_institution','icon addUser').'</div>'
	        : '')
	    . createBackLink (L_CONTENTS,'z_menu_contents')
	)
	->cast();
