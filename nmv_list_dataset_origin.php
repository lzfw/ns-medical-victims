<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');

// browsing options
$dbi->setUserVar ('sort',getUrlParameter('sort'),'work_group');
$dbi->setUserVar ('order',getUrlParameter('order'),'ASC');
$dbi->setUserVar ('skip',getUrlParameter('skip'),0);

// Select-Klauseln erstellen
$querystring_count = 'SELECT COUNT(*) AS total FROM nmv__dataset_origin'; // für Treffer gesamt
$querystring_items = 'SELECT ID_dataset_origin, work_group
                      FROM nmv__dataset_origin';

// Gesamtanzahl der Suchergebnisse feststellen
$query_count = $dbi->connection->query($querystring_count);
$total_results = $query_count->fetch_object();
$dbi->setUserVar('total_results',$total_results->total);

// order-klausel
$querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']} LIMIT ".($dbi->user['skip']).','.Z_LIST_ROWS_PAGE;

// query ausführen
$query_items = $dbi->connection->query($querystring_items.$querystring_orderby);

$layout
	->set('title','Dataset Origin')
	->set('content',
      '<p>Number of origin entries: ' . $total_results->total . ' </p>' .
      ($dbi->checkUserPermission('admin')
      ? '<div class="buttons">'.createButton ('New Dataset Origin','nmv_edit_dataset_origin','icon addUser').'</div>'
      : '') .
	    $dbi->getListView('nmv_dataset_origin_table',$query_items)
	    .($dbi->checkUserPermission('admin')
	        ? '<div class="buttons">'.createButton ('New Dataset Origin','nmv_edit_dataset_origin','icon addUser').'</div>'
	        : '')
	    .createBackLink (L_CONTENTS,'z_menu_contents')
	)
	->cast();
