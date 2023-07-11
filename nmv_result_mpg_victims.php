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
$exact_fields = array ('ID_victim', 'ID_dataset_origin', 'openUid');

// felder, die mit like gematcht werden (Trunkierung möglich, Diakritika distinkt, Basiszeichen ambivalent)
// --> If no diacritics are applied, it finds covers any combination: η would also return ἠ, ἦ or ἥ, while ἠ would find only ἠ.
$like_fields = array ();

//felder, die mit LIKE %xy% gematcht werden
$contain_fields = array ();

// felder, die mit like ODER exakt gematcht werden (Trunkierung möglich, Diakritika indistinkt)
// --> Arabic vowel signs are treated indistinctively: سبب would also return سَبَبٌ, and vice versa.
$double_fields = array ();

// fields that trigger special conditions when ticked
$ticked_fields = array ();

// fields with individual WHERE-clauses
$diy_fields = array ('surname', 'first_names');


// reconstruct GET-String (for scroll-function)
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
foreach ($ticked_fields as $field) {
	if (isset($_GET[$field]) && $_GET[$field] != '') $query[] = "$field={$_GET[$field]}";
}
foreach ($diy_fields as $field) {
	if (isset($_GET[$field]) && $_GET[$field] != '') $query[] = "$field={$_GET[$field]}";
}
$dbi->setUserVar('querystring',implode('&',$query));

// make select-clauses part one
$querystring_items = 'SELECT DISTINCT v.ID_victim, v.surname, v.first_names, v.birth_year,
										 v.birth_place, bc.country AS birth_country, n.nationality AS nationality_1938,
										 et.ethnic_group
											FROM nmv__victim v
											LEFT JOIN nmv__country bc							ON bc.ID_country = v.ID_birth_country
											LEFT JOIN nmv__nationality n 					ON n.ID_nationality = v.ID_nationality_1938
											LEFT JOIN nmv__ethnic_group et					ON et.ID_ethnic_group = v.ID_ethnic_group
											LEFT JOIN nmv__victim_name vn					ON v.ID_victim = vn.ID_victim
											'; // für Ergebnisliste
$querystring_where = array(); // for where-part of select clause
$querystring_where[] = "was_prisoner_assistant != 'prisoner assistant only' AND v.mpg_project = -1";

// MySQL-Zeichenfilter definieren (Trunkierungszeichen werden zu MySQL-Zeichen)
$filter_chars = array('%', '_', '*', '٭', '\'');
$replace_chars = array(' ', ' ', '%', '%', '_');

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
foreach ($contain_fields as $field) {
    if (getUrlParameter($field)) {
		$filtered_field = str_replace($filter_chars, $replace_chars, getUrlParameter($field));
		$querystring_where[] = "v.$field LIKE '%".$filtered_field."%'";
    }
}
foreach ($double_fields as $field) {
    if (getUrlParameter($field)) {
		$filtered_field = str_replace($filter_chars, $replace_chars, getUrlParameter($field));
		$querystring_where[] = "(v.$field LIKE '".$filtered_field."' OR v.$field = '".getUrlParameter($field)."')";
    }
}
//diy-WHERE-clauses for including Other Names in search
foreach ($diy_fields as $field) {
	if (getUrlParameter($field)) {
		if ($field == 'surname') {
			$filtered_field = str_replace($filter_chars, $replace_chars, getUrlParameter($field));
			$querystring_where[] = "(vn.surname LIKE '%".$filtered_field."%' OR v.$field LIKE '%".$filtered_field."%')";
		}
		if ($field == 'first_names') {
			$filtered_field = str_replace($filter_chars, $replace_chars, getUrlParameter($field));
			$querystring_where[] = "(vn.first_names LIKE '%".$filtered_field."%' OR v.$field LIKE '%".$filtered_field."%')";
		}
	}
}


// Add WHERE-clause and GROUP BY
$where_clause = '';
if (count($querystring_where) > 0) {
    $where_clause = ' WHERE '.implode(' AND ',$querystring_where);
		$where_clause_encoded = urlencode(utf8_encode($where_clause)); //encode for url-transfer to export
    $querystring_items .= $where_clause;
}
$querystring_items .= "GROUP BY v.ID_victim";


// Gesamtanzahl der Suchergebnisse feststellen
$querystring_count = "SELECT COUNT(*) AS total FROM ($querystring_items) AS xyz";
$query_count = $dbi->connection->query($querystring_count);
$total_results = $query_count->fetch_object();
$dbi->setUserVar('total_results',$total_results->total);

// order-klausel
$querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']} ";

// query ausführen
$query_items = $dbi->connection->query($querystring_items.$querystring_orderby);

// ausgabe der suchtermini
$suche_nach = array();
if (isset($_GET['ID_victim']) && $_GET['ID_victim']) $suche_nach[] = 'ID_victim = '.$_GET['ID_victim'];
if (isset($_GET['openUid']) && $_GET['openUid']) $suche_nach[] = 'openUid = '.$_GET['openUid'];
if (isset($_GET['surname']) && $_GET['surname']) $suche_nach[] = 'surname = '.$_GET['surname'];
if (isset($_GET['first_names']) && $_GET['first_names']) $suche_nach[] = 'first_names = '.$_GET['first_names'];
if (isset($_GET['ID_dataset_origin']) && $_GET['ID_dataset_origin']) {
	$workgroup = $dbi->connection->query('SELECT work_group FROM nmv__dataset_origin WHERE ID_dataset_origin = '.$_GET['ID_dataset_origin'])->fetch_row();
	$suche_nach[] = 'workgroup = '.$workgroup[0];
}

// breadcrumbs
$dbi->addBreadcrumb (L_SEARCH,'search.php');

// layout
$layout
	->set('title',L_RESULTS)
	->set('content',
        '<p>Search for: <em>'.implode(', ',$suche_nach).'</em><br>
				Number of results: '. $total_results->total. '</p>'
				. '<div class="buttons">'.createButton ('Export Table to .csv',"nmv_export.php?type=csv&entity=victim&where-clause=$where_clause_encoded",'icon download')
																 .createButton ('Export Table to .xls',"nmv_export.php?type=xls&entity=victim&where-clause=$where_clause_encoded",'icon download')
				. '</div>'
        .$dbi->getListView('table_nmv_victims_details',$query_items)
        .'<div class="buttons">'
				.createButton (L_MODIFY_SEARCH,'javascript:history.back()','icon search')
        .createButton (L_NEW_SEARCH,'search.php','icon search')
        .'</div>'
	)
	//->set('sidebar','<h3>'.L_HELP.'</h3>'.$dbi->getTextblock_HTML ('results'))
	->cast();

?>
