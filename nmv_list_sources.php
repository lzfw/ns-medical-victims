<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

// browsing options
$dbi->setUserVar ('sort',getUrlParameter('sort'),'source_title');
$dbi->setUserVar ('order',getUrlParameter('order'),'ASC');
$dbi->setUserVar ('skip',getUrlParameter('skip'),0);

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');

$options = '';
if ($dbi->checkUserPermission('edit')) {
		$options .= createSmallButton(L_EDIT,'nmv_edit_source?ID_source={ID_source}','icon edit');
}
if ($dbi->checkUserPermission('admin')) {
		$options .= createSmallButton(L_DELETE,'nmv_remove_source?ID_source={ID_source}','icon delete');
}

// Select-Klauseln erstellen
$querystring_count = 'SELECT COUNT(ID_source) AS total FROM nmv__source v'; // für Treffer gesamt
$querystring_items = 'SELECT `ID_source`, `source_title`, `medium`, `description`
                FROM nmv__source'; // für Ergebnisliste
$querystring_where = array(); // für Filter

// Gesamtanzahl der Suchergebnisse feststellen
$query_count = $dbi->connection->query($querystring_count);
$total_results = $query_count->fetch_object();
$dbi->setUserVar('total_results',$total_results->total);

// order-klausel
$querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']} LIMIT ".($dbi->user['skip']).','.Z_LIST_ROWS_PAGE;

// query ausführen
$query_items = $dbi->connection->query($querystring_items.$querystring_orderby);

$layout
	->set('title','Sources')
	->set('content',
	    $dbi->getListView('nmv_sources_table',$query_items)
	    .($dbi->checkUserPermission('edit')
	        ? '<div class="buttons">'.createButton ('New Source','nmv_edit_source','icon add').'</div>'
	        : '')
	    .createBackLink (L_CONTENTS,'z_menu_contents')
	)
	->cast();
