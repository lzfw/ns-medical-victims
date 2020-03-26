<?php
/**
* define searchqueries for search concerning victims registered in the MPG-project
*
*
*
*/

// CMS file: search results (public)
// last known update: 2020

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
$dbi->setUserVar ('sort',getUrlParameter('sort'),'surname');
$dbi->setUserVar ('order',getUrlParameter('order'),'ASC');
$dbi->setUserVar ('skip',getUrlParameter('skip'),0);


// zu durchsuchende felder und suchsystematik definieren:

// felder, die immer exakt gematcht werden (Trunkierung nicht möglich, Diakritika distinkt, Basiszeichen distinkt)
$exact_fields = array ('ID_victim', 'ID_dataset_origin');

// felder, die mit like gematcht werden (Trunkierung möglich, Diakritika distinkt, Basiszeichen ambivalent)
// --> If no diacritics are applied, it finds covers any combination: η would also return ἠ, ἦ or ἥ, while ἠ would find only ἠ.
$like_fields = array ('surname', 'first_names');

// felder, die mit like ODER exakt gematcht werden (Trunkierung möglich, Diakritika indistinkt)
// --> Arabic vowel signs are treated indistinctively: سبب would also return سَبَبٌ, and vice versa.
$double_fields = array ();

// fields that trigger special conditions when ticked
$ticked_fields = array ('cause_of_death');

// reconstruct GET-String (for scroll-function)
$query = array();
foreach ($exact_fields as $field) {
	if (isset($_GET[$field]) && $_GET[$field] != '') $query[] = "$field={$_GET[$field]}";
}
foreach ($like_fields as $field) {
	if (isset($_GET[$field]) && $_GET[$field] != '') $query[] = "$field={$_GET[$field]}";
}
foreach ($double_fields as $field) {
	if (isset($_GET[$field]) && $_GET[$field] != '') $query[] = "$field={$_GET[$field]}";
}
foreach ($ticked_fields as $field) {
	if (isset($_GET[$field]) && $_GET[$field] != '') $query[] = "$field={$_GET[$field]}";
}
$dbi->setUserVar('querystring',implode('&',$query));

// make select-clauses part one
$querystring_items = 'SELECT v.ID_victim, v.surname, v.first_names FROM nmv__victim v'; // für Ergebnisliste
$querystring_where = array(); // for where-part of select clause



// MySQL-Zeichenfilter definieren (Trunkierungszeichen werden zu MySQL-Zeichen)
$filter_chars = array("'", '%', '_', '*', '٭');
$replace_chars = array('', ' ', ' ', '%', '%');

// WHERE Strings zusammenbauen
$querystring_where[] = "v.mpg_project = -1";

foreach ($exact_fields as $field) {
    if (getUrlParameter($field)) {
        $querystring_where[] = "v.$field = '".getUrlParameter($field)."'";
    }
}
foreach ($like_fields as $field) {
    if (getUrlParameter($field)) {
		$filtered_field = str_replace($filter_chars, $replace_chars, getUrlParameter($field));
		$querystring_where[] = "TRIM(v.$field) LIKE TRIM('".$filtered_field."')";
    }
}
foreach ($double_fields as $field) {
    if (getUrlParameter($field)) {
		$filtered_field = str_replace($filter_chars, $replace_chars, getUrlParameter($field));
		$querystring_where[] = "(v.$field LIKE '".$filtered_field."' OR v.$field = '".getUrlParameter($field)."')";
    }
}
if (getUrlParameter($ticked_fields[0])) {
  $querystring_where[] = "(v.cause_of_death LIKE '%executed%'
                              OR v.cause_of_death LIKE '%execution%'
                              OR v.cause_of_death LIKE '%exekution%')";
}

if (count($querystring_where) > 0) {
    $querystring_items .= ' WHERE '.implode(' AND ',$querystring_where);
}


// append select-clauses part two for other names
$querystring_items .= ' UNION ';
$querystring_items .= '	SELECT v.ID_victim, v.surname, v.first_names
												FROM nmv__victim_name o
												INNER JOIN nmv__victim v
												ON o.ID_victim = v.ID_victim';
$querystring_other_where = array(); // für Filter


$querystring_other_where[] = "v.mpg_project = -1";

foreach ($exact_fields as $field) {
    if (getUrlParameter($field)) {
        $querystring_other_where[] = "v.$field = '".getUrlParameter($field)."'";
    }
}
if (getUrlParameter($like_fields[0])) {
  $filtered_field = str_replace($filter_chars, $replace_chars, getUrlParameter($like_fields[0]));
  $querystring_other_where[] = "TRIM(o.victim_name) LIKE TRIM('".$filtered_field."')";
}
if (getUrlParameter($like_fields[1])) {
  $filtered_field = str_replace($filter_chars, $replace_chars, getUrlParameter($like_fields[1]));
  $querystring_other_where[] = "TRIM(o.victim_first_names) LIKE TRIM('".$filtered_field."')";
}
if (getUrlParameter($ticked_fields[0])) {
  $querystring_other_where[] = "(v.cause_of_death LIKE '%executed%'
                              OR v.cause_of_death LIKE '%execution%'
                              OR v.cause_of_death LIKE '%exekution%')";
}

if (count($querystring_other_where) > 0) {
    $querystring_items .= ' WHERE '.implode(' AND ',$querystring_other_where);
}

// for debugging
//echo $querystring_items;

// Gesamtanzahl der Suchergebnisse feststellen
$querystring_count = "SELECT COUNT(*) AS total FROM ($querystring_items) AS xyz";
$query_count = $dbi->connection->query($querystring_count);
$total_results = $query_count->fetch_object();
$dbi->setUserVar('total_results',$total_results->total);

// order-klausel
$querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']} LIMIT ".($dbi->user['skip']).','.Z_LIST_ROWS_PAGE;

// query ausführen
$query_items = $dbi->connection->query($querystring_items.$querystring_orderby);

// ausgabe der suchtermini
$suche_nach = array();
if (isset($_GET['ID_victim']) && $_GET['ID_victim']) $suche_nach[] = 'ID_victim = '.$_GET['ID_victim'];
if (isset($_GET['surname']) && $_GET['surname']) $suche_nach[] = 'surname = '.$_GET['surname'];
if (isset($_GET['first_names']) && $_GET['first_names']) $suche_nach[] = 'first_names = '.$_GET['first_names'];

// breadcrumbs
$dbi->addBreadcrumb (L_SEARCH,'search.php');

// layout
$layout
	->set('title',L_RESULTS)
	->set('content',
        '<p>Search for: <em>'.implode(', ',$suche_nach).'</em></p>'
        .$dbi->getListView('nmv_victims',$query_items)
        .'<div class="buttons">'
        .createButton (L_MODIFY_SEARCH,'search.php?'.$dbi->getUserVar('querystring'),'icon search')
        .createButton (L_NEW_SEARCH,'search.php','icon search')
        .'</div>'
	)
	->set('sidebar','<h3>'.L_HELP.'</h3>'.$dbi->getTextblock_HTML ('results'))
	->cast();

?>
