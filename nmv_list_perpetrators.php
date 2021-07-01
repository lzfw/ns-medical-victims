<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');

// browsing options
$dbi->setUserVar ('sort',getUrlParameter('sort'),'surname');
$dbi->setUserVar ('order',getUrlParameter('order'),'ASC');
$dbi->setUserVar ('skip',getUrlParameter('skip'),0);

$options = '';
if ($dbi->checkUserPermission('edit')) {
		$options .= createSmallButton(L_EDIT,'nmv_edit_perpetrator?ID_perpetrator={ID_perpetrator}','icon edit');
}
if ($dbi->checkUserPermission('admin')) {
		$options .= createSmallButton(L_DELETE,'nmv_remove_perpetrator?ID_perpetrator={ID_perpetrator}','icon delete');
}

// Select-Klauseln erstellen
$querystring_count = 'SELECT COUNT(*) AS total FROM nmv__perpetrator p'; // für Treffer gesamt
$querystring_items = 'SELECT `ID_perpetrator`, `surname`, `first_names`, `birth_place` FROM nmv__perpetrator p'; // für Ergebnisliste
$querystring_where = array(); // für Filter

/*
$content = '';

$content .= buildTableFromQuery(
    'SELECT `ID_perpetrator`, `surname`, `first_names`, `birth_place`
        FROM nmv__perpetrator
        ORDER BY `surname` DESC
        LIMIT 10',
    ['<a href="nmv_view_perpetrator?ID_perpetrator={ID_perpetrator}">{surname}</a>', '{first_names}', '{ID_perpetrator}', '{birth_place}', $options],
    ['surname', 'first names', 'id', 'birth place', 'options'],
    'grid');

if ($dbi->checkUserPermission('edit')) {
	$content .= '<div class="buttons">';
	$content .= createButton ('New Perpetrator','nmv_edit_perpetrator','icon addUser');
	$content .= '</div>';
}
$content .= createBackLink (L_CONTENTS,'z_menu_contents');

$layout
	->set('title','Perpetrators')
	->set('content',$content)
	->cast();
*/

// Gesamtanzahl der Suchergebnisse feststellen
$query_count = $dbi->connection->query($querystring_count);
$total_results = $query_count->fetch_object();
$dbi->setUserVar('total_results',$total_results->total);

// order-klausel
//$querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']} LIMIT ".($dbi->user['skip']).','.Z_LIST_ROWS_PAGE;
$querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']}";


// query ausführen
$query_items = $dbi->connection->query($querystring_items.$querystring_orderby);

$layout
	->set('title','Perpetrators')
	->set('content',
	    $dbi->getListView('nmv_perpetrators_table',$query_items)
	    .($dbi->checkUserPermission('edit')
	        ? '<div class="buttons">'.createButton ('New Perpetrator','nmv_edit_perpetrator','icon addUser').'</div>'
	        : '')
	    .createBackLink (L_CONTENTS,'z_menu_contents')
	)
	->cast();
