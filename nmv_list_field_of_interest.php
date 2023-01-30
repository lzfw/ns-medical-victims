<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');

// browsing options
$dbi->setUserVar ('sort',getUrlParameter('sort'),'field_of_interest');
$dbi->setUserVar ('order',getUrlParameter('order'),'ASC');
$dbi->setUserVar ('skip',getUrlParameter('skip'),0);

// Select-Klauseln erstellen
$querystring_count = 'SELECT COUNT(*) AS total FROM nmv__field_of_interest f'; // für Treffer gesamt
$querystring_items = 'SELECT ID_foi, field_of_interest
                      FROM nmv__field_of_interest f';

// Gesamtanzahl der Suchergebnisse feststellen
$query_count = $dbi->connection->query($querystring_count);
$total_results = $query_count->fetch_object();
$dbi->setUserVar('total_results',$total_results->total);

// order-klausel
$querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']}";

// query ausführen
$query_items = $dbi->connection->query($querystring_items.$querystring_orderby);

$layout
	->set('title','Field of Interest')
	->set('content',
      '<p>Number of Field of Interest entries: ' . $total_results->total . ' </p>' .
      ($dbi->checkUserPermission('admin')
      ? '<div class="buttons">'.createButton ('New field of Interest','nmv_edit_field_of_interest','icon addUser').'</div>'
      : '') .
	    $dbi->getListView('nmv_field_of_interest_table',$query_items)
	    .($dbi->checkUserPermission('admin')
	        ? '<div class="buttons">'.createButton ('New Field of Interest','nmv_edit_field_of_interest','icon addUser').'</div>'
	        : '')
	    .createBackLink (L_CONTENTS,'z_menu_contents')
	)
	->cast();
