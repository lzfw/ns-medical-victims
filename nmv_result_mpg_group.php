<?php
/**
* define searchqueries for filtering victims from MPG Project
*
*
*
*/

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
$exact_fields = array ('ID_dataset_origin', 'ID_institution');

// felder, die mit like gematcht werden (Trunkierung möglich, Diakritika distinkt, Basiszeichen ambivalent)
// --> If no diacritics are applied, it finds covers any combination: η would also return ἠ, ἦ or ἥ, while ἠ would find only ἠ.
$like_fields = array ();

// felder, die mit like ODER exakt gematcht werden (Trunkierung möglich, Diakritika indistinkt)
// --> Arabic vowel signs are treated indistinctively: سبب would also return سَبَبٌ, and vice versa.
$double_fields = array ();

// fields that trigger special conditions when ticked
$ticked_fields = array ('cause_of_death', 'prisoner_of_war', 'psychiatric_patient');

// fields with special queries
//$special_fields = array ('ID_institution');

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
// foreach ($special_fields as $field) {
// 	if (isset($_GET[$field]) && $_GET[$field] != '') $query[] = "$field={$_GET[$field]}";
// }
$dbi->setUserVar('querystring',implode('&',$query));

// make select-clauses
$querystring_count = 'SELECT COUNT(v.ID_victim) AS total FROM nmv__victim v'; // für Treffer gesamt
$querystring_items = 'SELECT DISTINCT v.ID_victim, v.surname, v.first_names
											FROM nmv__victim v
											LEFT JOIN nmv__med_history_brain b ON v.ID_victim = b.ID_victim
											LEFT JOIN nmv__imprisoniation i    ON v.ID_victim = i.ID_victim'; // für Ergebnisliste
$querystring_where = array(); // for where-part of select clause



// MySQL-Zeichenfilter definieren (Trunkierungszeichen werden zu MySQL-Zeichen)
$filter_chars = array("'", '%', '_', '*', '٭');
$replace_chars = array('', ' ', ' ', '%', '%');

// make WHERE conditions
$querystring_where[] = "v.mpg_project = -1";

foreach ($exact_fields as $field) {
    if (getUrlParameter($field)) {
				if ($field == 'ID_institution'):
        	$querystring_where[] = "b.$field = '".getUrlParameter($field)."'";
				else:
					$querystring_where[] = "v.$field = '".getUrlParameter($field)."'";
				endif;
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
if (getUrlParameter($ticked_fields[0])) {
  $querystring_where[] = "(v.cause_of_death LIKE '%executed%'
                              OR v.cause_of_death LIKE '%execution%'
                              OR v.cause_of_death LIKE '%exekution%')";
}
if (getUrlParameter($ticked_fields[1])) {
  $querystring_where[] = "(i.ID_classification = 7)";
}
if (getUrlParameter($ticked_fields[2])) {
  $querystring_where[] = "(i.ID_classification = 5)";
}

if (count($querystring_where) > 0) {
    $querystring_count .= ' WHERE '.implode(' AND ',$querystring_where);
    $querystring_items .= ' WHERE '.implode(' AND ',$querystring_where);
}


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
