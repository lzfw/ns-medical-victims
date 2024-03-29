<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');

// browsing options
$dbi->setUserVar ('sort',getUrlParameter('sort'),'tissue_form');
$dbi->setUserVar ('order',getUrlParameter('order'),'ASC');
$dbi->setUserVar ('skip',getUrlParameter('skip'),0);

// Select-Klauseln erstellen
$querystring_count = 'SELECT COUNT(*) AS total FROM nmv__tissue_form'; // für Treffer gesamt
$querystring_items = 'SELECT ID_tissue_form, tissue_form
                      FROM nmv__tissue_form';

// Gesamtanzahl der Suchergebnisse feststellen
$query_count = $dbi->connection->query($querystring_count);
$total_results = $query_count->fetch_object();
$dbi->setUserVar('total_results',$total_results->total);

// order-klausel
$querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']}";

// query ausführen
$query_items = $dbi->connection->query($querystring_items.$querystring_orderby);

$layout
	->set('title','Tissue Form')
	->set('content',
      '<p>Number of tissue form entries: ' . $total_results->total . '</p>' .
      ($dbi->checkUserPermission('admin')
	        ? '<div class="buttons">'.createButton ('New Tissue Form','nmv_edit_tissue_form','icon addUser').'</div>'
	        : '') .
	    $dbi->getListView('nmv_tissue_form_table',$query_items)
	    .($dbi->checkUserPermission('admin')
	        ? '<div class="buttons">'.createButton ('New Tissue Form','nmv_edit_tissue_form','icon addUser').'</div>'
	        : '')
	    .createBackLink (L_CONTENTS,'z_menu_contents')
	)
	->cast();
