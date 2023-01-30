<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');

// browsing options
$dbi->setUserVar ('sort',getUrlParameter('sort'),'classification');
$dbi->setUserVar ('order',getUrlParameter('order'),'ASC');
$dbi->setUserVar ('skip',getUrlParameter('skip'),0);

// Select-Klauseln erstellen
$querystring_count = 'SELECT COUNT(*) AS total FROM nmv__perpetrator_classification c'; // für Treffer gesamt
$querystring_items = 'SELECT ID_perp_class, classification
                      FROM nmv__perpetrator_classification c';

// Gesamtanzahl der Suchergebnisse feststellen
$query_count = $dbi->connection->query($querystring_count);
$total_results = $query_count->fetch_object();
$dbi->setUserVar('total_results',$total_results->total);

// order-klausel
$querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']}";

// query ausführen
$query_items = $dbi->connection->query($querystring_items.$querystring_orderby);

$layout
	->set('title','Perpetrator Classification')
	->set('content',
      '<p>Number of occupation entries: ' . $total_results->total . '</p>' .
      ($dbi->checkUserPermission('admin')
	        ? '<div class="buttons">'.createButton ('New Perpetrator Classification','nmv_edit_perpetrator_classification','icon addUser').'</div>'
	        : '') .
	    $dbi->getListView('nmv_perpetrator_classification_table',$query_items)
	    .($dbi->checkUserPermission('admin')
	        ? '<div class="buttons">'.createButton ('New Perpetrator Classification','nmv_edit_perpetrator_classification','icon addUser').'</div>'
	        : '')
	    .createBackLink (L_CONTENTS,'z_menu_contents')
	)
	->cast();
