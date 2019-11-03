<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

// browsing options
$dbi->setUserVar ('sort',getUrlParameter('sort'),'surname');
$dbi->setUserVar ('order',getUrlParameter('order'),'ASC');
$dbi->setUserVar ('skip',getUrlParameter('skip'),0);

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');

$options = '';
if ($dbi->checkUserPermission('edit')) {
		$options .= createSmallButton(L_EDIT,'nmv_edit_victim?ID_victim={ID_victim}','icon edit');
}
if ($dbi->checkUserPermission('admin')) {
		$options .= createSmallButton(L_DELETE,'nmv_remove_victim?ID_victim={ID_victim}','icon delete');
}
$options .= createSmallButton("medical history",'nmv_list_med_hist?ID_victim={ID_victim}','icon report-paper');

// Select-Klauseln erstellen
$querystring_count = 'SELECT COUNT(v.ID_victim) AS total FROM nmv__victim v'; // für Treffer gesamt
$querystring_items = 'SELECT `ID_victim`, `surname`, `first_names`, `birth_place` FROM nmv__victim'; // für Ergebnisliste
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
	->set('title','Victims')
	->set('content',
	    $dbi->getListView('nmv_victims_table',$query_items)
	    .($dbi->checkUserPermission('edit')
	        ? '<div class="buttons">'.createButton ('New Victim','nmv_edit_victim','icon addUser').'</div>'
	        : '')
	    .createBackLink (L_CONTENTS,'z_menu_contents')
	)
	->cast();
