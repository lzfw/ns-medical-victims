<?php
// CMS file: search results (public)
// last known update: 2013-01-22

require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

// fallback, if no parameters have been transmitted
if (implode('',$_GET) == '') {
	header("Location: search.php");
	exit;
}

// url parameters
$dbi->setUserVar ('view',getUrlParameter('view'),'default');
// browsing options
$dbi->setUserVar ('sort',getUrlParameter('sort'),'lit_title');
$dbi->setUserVar ('order',getUrlParameter('order'),'ASC');
$dbi->setUserVar ('skip',getUrlParameter('skip'),0);


// zu durchsuchende felder und suchsystematik definieren:

// felder, die immer exakt gematcht werden (Trunkierung nicht möglich, Diakritika distinkt, Basiszeichen distinkt)
$exact_fields = array ('ID_literature');

// felder, die mit like gematcht werden, Leerzeichen an Anfang und Ende werden getrimmt (Trunkierung möglich, Diakritika distinkt, Basiszeichen ambivalent)
// --> If no diacritics are applied, it finds covers any combination: η would also return ἠ, ἦ or ἥ, while ἠ would find only ἠ.
$like_fields = array ('lit_year');

//felder, die mit LIKE %xy% gematcht werden
$contain_fields = array('authors', 'lit_title');

// felder, die mit like ODER exakt gematcht werden (Trunkierung möglich, Diakritika indistinkt)
// --> Arabic vowel signs are treated indistinctively: سبب would also return سَبَبٌ, and vice versa.
$double_fields = array ();

// GET-String rekonstruieren (für Blätterfunktion)
$query = array();
foreach ($exact_fields as $field) {
	if (isset($_GET[$field]) && $_GET[$field] != '') $query[] = "$field={$_GET[$field]}";
}
foreach ($like_fields as $field) {
	if (isset($_GET[$field]) && $_GET[$field] != '') $query[] = "$field={$_GET[$field]}";
}
foreach ($contain_fields as $field) {
	if (isset($_GET[$field]) && $_GET[$field] != '') $query[] = "$field={$_GET[$field]}";
}
foreach ($double_fields as $field) {
	if (isset($_GET[$field]) && $_GET[$field] != '') $query[] = "$field={$_GET[$field]}";
}
$dbi->setUserVar('querystring',implode('&',$query));

// Select-Klauseln erstellen
$querystring_count = 'SELECT COUNT(l.ID_literature) AS total FROM nmv__literature l'; // für Treffer gesamt
$querystring_items = 'SELECT l.ID_literature, l.lit_title, l.authors,
                        l.lit_year FROM nmv__literature l'; // für Ergebnisliste
$querystring_where = array(); // für Filter

// MySQL-Zeichenfilter definieren (Trunkierungszeichen werden zu MySQL-Zeichen)
$filter_chars = array("'", '%', '_', '*', '٭');
$replace_chars = array('', ' ', ' ', '%', '%');

// Strings zusammenbauen
foreach ($exact_fields as $field) {
    if (getUrlParameter($field)) {
        $querystring_where[] = "l.$field = '".getUrlParameter($field)."'";
    }
}
foreach ($like_fields as $field) {
    if (getUrlParameter($field)) {
		$filtered_field = str_replace($filter_chars, $replace_chars, getUrlParameter($field));
		$querystring_where[] = "TRIM(l.$field) LIKE TRIM('".$filtered_field."')";
    }
}
foreach ($contain_fields as $field) {
    if (getUrlParameter($field)) {
		$filtered_field = str_replace($filter_chars, $replace_chars, getUrlParameter($field));
		$querystring_where[] = "l.$field LIKE '%".$filtered_field."%'";
    }
}
foreach ($double_fields as $field) {
    if (getUrlParameter($field)) {
		$filtered_field = str_replace($filter_chars, $replace_chars, getUrlParameter($field));
		$querystring_where[] = "(l.$field LIKE '".$filtered_field."' OR l.$field = '".getUrlParameter($field)."')";
    }
}

if (count($querystring_where) > 0) {
    $querystring_count .= ' WHERE '.implode(' AND ',$querystring_where);
    $querystring_items .= ' WHERE '.implode(' AND ',$querystring_where);
}

// Gesamtanzahl der Suchergebnisse feststellen
$query_count = $dbi->connection->query($querystring_count);
$total_results = $query_count->fetch_object();
$dbi->setUserVar('total_results',$total_results->total);

// order-klausel
$querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']} LIMIT ".($dbi->user['skip']).','.Z_LIST_ROWS_PAGE;

// query ausführen
$query_items = $dbi->connection->query($querystring_items.$querystring_orderby);

// ausgabe der suchtermini
$suche_nach = array();
if (isset($_GET['ID_literature']) && $_GET['ID_literature']) $suche_nach[] = 'ID = '.$_GET['ID_literature'];
if (isset($_GET['lit_title']) && $_GET['lit_title']) $suche_nach[] = 'title = '.$_GET['lit_title'];
if (isset($_GET['authors']) && $_GET['authors']) $suche_nach[] = 'authors = '.$_GET['authors'];
if (isset($_GET['lit_year']) && $_GET['lit_year']) $suche_nach[] = 'year = '.$_GET['lit_year'];

// breadcrumbs
$dbi->addBreadcrumb (L_SEARCH,'search.php');

// layout
$layout
	->set('title',L_RESULTS)
	->set('content',
        '<p>Search for: <em>'.implode(', ',$suche_nach).'</em></p>'
        .$dbi->getListView('nmv_literature_table',$query_items)
        .'<div class="buttons">'
        .createButton (L_MODIFY_SEARCH,'search.php?'.$dbi->getUserVar('querystring'),'icon search')
        .createButton (L_NEW_SEARCH,'search.php','icon search')
        .'</div>'
	)
	//->set('sidebar','<h3>'.L_HELP.'</h3>'.$dbi->getTextblock_HTML ('results'))
	->cast();

?>
