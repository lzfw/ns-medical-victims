<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

// list browsing options
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

// create SELECT clause
$querystring_count = 'SELECT COUNT(ID_source) AS total FROM nmv__source v'; // für Treffer gesamt
$querystring_items = 'SELECT `ID_source`, `source_title`, `medium`, `signature`, `description`
                FROM nmv__source'; // für Ergebnisliste
$querystring_where = array(); // für Filter

// count total number of search results
$query_count = $dbi->connection->query($querystring_count);
$total_results = $query_count->fetch_object();
$dbi->setUserVar('total_results',$total_results->total);

// SQL ORDER BY clause
$querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']}";

// execute queries
$query_items = $dbi->connection->query($querystring_items.$querystring_orderby);

$layout
	->set('title','Sources')
	->set('content',
			'<p>Number of source entries: ' . $total_results->total . '</p>' .
			($dbi->checkUserPermission('edit')
	        ? '<div class="buttons">'.createButton ('New Source','nmv_edit_source','icon add').'</div>'
	        : '') .
	    $dbi->getListView('nmv_sources_table',$query_items)
	    .($dbi->checkUserPermission('edit')
	        ? '<div class="buttons">'.createButton ('New Source','nmv_edit_source','icon add').'</div>'
	        : '')
	    .createBackLink (L_CONTENTS,'z_menu_contents')
	)
	->cast();
