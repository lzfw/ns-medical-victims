<?php
// CMS file: remote management
// last known update: 2013-01-22

require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin.php');


$dbi->setUserVar ('sort',getUrlParameter('sort'),'datetime');
$dbi->setUserVar ('order',getUrlParameter('order'),'DESC');
$dbi->setUserVar ('skip',getUrlParameter('skip'),0);

// Select-Klauseln erstellen
$querystring_count = 'SELECT COUNT(log_id) AS total FROM z_log l'; // für Treffer gesamt
$querystring_items = 'SELECT `datetime`, `operation`, `entity`, `result`, `row_id`, `details`
        FROM z_log l'; // für Ergebnisliste
$querystring_where = array(); // für Filter

// Gesamtanzahl der Suchergebnisse feststellen
$query_count = $dbi->connection->query($querystring_count);
$total_results = $query_count->fetch_object();
$dbi->setUserVar('total_results',$total_results->total);

// order-klausel
$querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']} LIMIT ".($dbi->user['skip']).','.Z_LIST_ROWS_PAGE;

// query ausführen
$query_items = $dbi->connection->query($querystring_items.$querystring_orderby);


$content = $dbi->getListView('z_log_table',$query_items) .
    createBackLink (L_ADMIN,'z_menu_admin.php');

$layout
	->set('title',L_VIEW_LOG)
	->set('content',$content)
	->cast();
