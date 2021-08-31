<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');

// browsing options
$dbi->setUserVar ('sort',getUrlParameter('sort'),'english');
$dbi->setUserVar ('order',getUrlParameter('order'),'ASC');
$dbi->setUserVar ('skip',getUrlParameter('skip'),0);

// Select-Klauseln erstellen
$querystring_count = 'SELECT COUNT(*) AS total FROM nmv__victim_evaluation_compensation'; // für Treffer gesamt
$querystring_items = 'SELECT ID_compensation, english, deutsch
                      FROM nmv__victim_evaluation_compensation';

// Gesamtanzahl der Suchergebnisse feststellen
$query_count = $dbi->connection->query($querystring_count);
$total_results = $query_count->fetch_object();
$dbi->setUserVar('total_results',$total_results->total);

// order-klausel
$querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']} LIMIT ".($dbi->user['skip']).','.Z_LIST_ROWS_PAGE;

// query ausführen
$query_items = $dbi->connection->query($querystring_items.$querystring_orderby);

$layout
	->set('title','Victim Evaluation - Compensation')
	->set('content',
      '<p>Number of compensation entries: ' . $total_results->total . '</p>' .
      ($dbi->checkUserPermission('admin')
	        ? '<div class="buttons">'.createButton ('New Compensation','nmv_edit_victim_evaluation_compensation','icon addUser').'</div>'
	        : '') .
	    $dbi->getListView('nmv_victim_evaluation_compensation_table',$query_items)
	    .($dbi->checkUserPermission('admin')
	        ? '<div class="buttons">'.createButton ('New Compensation','nmv_edit_victim_evaluation_compensation','icon addUser').'</div>'
	        : '')
	    .createBackLink (L_CONTENTS,'z_menu_contents')
	)
	->cast();
