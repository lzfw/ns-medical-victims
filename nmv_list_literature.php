<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');

$dbi->setUserVar ('sort',getUrlParameter('sort'),'authors');
$dbi->setUserVar ('order',getUrlParameter('order'),'ASC');
$dbi->setUserVar ('skip',getUrlParameter('skip'),0);

$options = '';

if ($dbi->checkUserPermission('edit')) {
		$options .= createSmallButton(L_EDIT,'nmv_edit_literature?ID_literature={ID_literature}','icon edit');
}
if ($dbi->checkUserPermission('admin')) {
		$options .= createSmallButton(L_DELETE,'nmv_remove_literature?ID_literature={ID_literature}','icon delete');
}

// Select-Klauseln erstellen
$querystring_count = 'SELECT COUNT(ID_literature) AS total FROM nmv__literature v'; // für Treffer gesamt
$querystring_items =  'SELECT `ID_literature`, `authors`, `lit_year`, `lit_title`
                FROM nmv__literature'; // für Ergebnisliste
$querystring_where = array(); // für Filter

// Gesamtanzahl der Suchergebnisse feststellen
$query_count = $dbi->connection->query($querystring_count);
$total_results = $query_count->fetch_object();
$dbi->setUserVar('total_results',$total_results->total);

// order-klausel
$querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']}";

// query ausführen
$query_items = $dbi->connection->query($querystring_items.$querystring_orderby);

$layout
	->set('title','Literature')
	->set('content',
			'<p>Number of literature entries: ' . $total_results->total . '</p>' .
	    $dbi->getListView('nmv_literature_table',$query_items)
	    .($dbi->checkUserPermission('edit')
	        ? '<div class="buttons">'.createButton ('New Literature','nmv_edit_literature','icon add').'</div>'
	        : '')
	    .createBackLink (L_CONTENTS,'z_menu_contents')
	)
	->cast();
