<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');

// browsing options
$dbi->setUserVar ('sort',getUrlParameter('sort'),'diagnosis');
$dbi->setUserVar ('order',getUrlParameter('order'),'ASC');
$dbi->setUserVar ('skip',getUrlParameter('skip'),0);

// Select-Klauseln erstellen
$querystring_count = 'SELECT COUNT(*) AS total FROM nmv__diagnosis_tag'; // für Treffer gesamt
$querystring_items = 'SELECT ID_diagnosis, diagnosis
                      FROM nmv__diagnosis_tag';

// Gesamtanzahl der Suchergebnisse feststellen
$query_count = $dbi->connection->query($querystring_count);
$total_results = $query_count->fetch_object();
$dbi->setUserVar('total_results',$total_results->total);

// order-klausel
$querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']}";

// query ausführen
$query_items = $dbi->connection->query($querystring_items.$querystring_orderby);

$layout
	->set('title','Diagnosis')
	->set('content',
      '<p>Number of diagnosis entries: ' . $total_results->total . ' </p>' .
      ($dbi->checkUserPermission('admin')
      ? '<div class="buttons">'.createButton ('New Diagnosis','nmv_edit_diagnosis','icon addUser').'</div>'
      : '') .
      $dbi->getListView('nmv_diagnosis_table',$query_items)
	    .($dbi->checkUserPermission('admin')
	        ? '<div class="buttons">'.createButton ('New Diagnosis','nmv_edit_diagnosis','icon addUser').'</div>'
	        : '')
	    .createBackLink (L_CONTENTS,'z_menu_contents')
	)
	->cast();
