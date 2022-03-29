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
$exact_fields = array ('ID_dataset_origin', 'ID_institution', 'ID_tissue_institution');

// felder, die mit like gematcht werden (Trunkierung möglich, Diakritika distinkt, Basiszeichen ambivalent)
// --> If no diacritics are applied, it finds covers any combination: η would also return ἠ, ἦ or ἥ, while ἠ would find only ἠ.
$like_fields = array ();

// felder, die mit like ODER exakt gematcht werden (Trunkierung möglich, Diakritika indistinkt)
// --> Arabic vowel signs are treated indistinctively: سبب would also return سَبَبٌ, and vice versa.
$double_fields = array ();

// fields that trigger special conditions when ticked
$ticked_fields = array ('cause_of_death', 'prisoner_of_war', 'psychiatric_patient', 'beddies_database', 'rothemund_list', 'schlote_list', 'm_series');

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
$querystring_items = 'SELECT DISTINCT v.ID_victim, v.surname, v.first_names, v.birth_year,
															bc.english AS birth_country, v.birth_place, et.english AS ethnic_group,
															n.english AS nationality_1938
											FROM nmv__victim v
											LEFT JOIN nmv__country bc						ON v.ID_birth_country = bc.ID_country
											LEFT JOIN nmv__nationality n 				ON v.nationality_1938 = n.ID_nationality
											LEFT JOIN nmv__ethnicgroup et 			ON v.ethnic_group = et.ID_ethnicgroup
											LEFT JOIN nmv__med_history_brain b 	ON v.ID_victim = b.ID_victim
											LEFT JOIN nmv__med_history_hosp h   ON v.ID_victim = h.ID_victim
											LEFT JOIN nmv__victim_experiment ve ON v.ID_victim = ve.ID_victim
											LEFT JOIN nmv__experiment e 				ON e.ID_experiment = ve.ID_experiment
											LEFT JOIN nmv__med_history_tissue t ON v.ID_victim = t.ID_victim
											LEFT JOIN nmv__imprisoniation i    	ON v.ID_victim = i.ID_victim
											LEFT JOIN nmv__victim_source vs 	 	ON v.ID_victim = vs.ID_victim'; // für Ergebnisliste
$querystring_where = array(); // for where-part of select clause



// MySQL-Zeichenfilter definieren (Trunkierungszeichen werden zu MySQL-Zeichen)
$filter_chars = array("'", '%', '_', '*', '٭');
$replace_chars = array('', ' ', ' ', '%', '%');

// make WHERE conditions
$querystring_where[] = "v.mpg_project = -1";

foreach ($exact_fields as $field) {
    if (getUrlParameter($field)) {
			if ($field == 'ID_institution'){
				$querystring_where[] = "(b.$field = '".getUrlParameter($field)."'
				OR h.$field = '".getUrlParameter($field)."'
				OR e.$field = '".getUrlParameter($field)."')";
			}
			elseif ($field == 'ID_tissue_institution'){
				$querystring_where[] = "t.ID_institution = '".getUrlParameter($field)."'";
			}
			else{
				$querystring_where[] = "v.$field = '".getUrlParameter($field)."'";
			}

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
if (getUrlParameter($ticked_fields[1])) {
  $querystring_where[] = "i.ID_classification = 7";
}
if (getUrlParameter($ticked_fields[2])) {
  $querystring_where[] = "i.ID_classification = 5";
}
if (getUrlParameter($ticked_fields[3])) {
  $querystring_where[] = "vs.ID_source = 207";
}
if (getUrlParameter($ticked_fields[4])) {
  $querystring_where[] = "vs.ID_source IN (319, 299, 287)";
}
if (getUrlParameter($ticked_fields[5])) {
  $querystring_where[] = "vs.ID_source = 304";
}
if (getUrlParameter($ticked_fields[6])) {
  $querystring_where[] = "(b.ref_no LIKE 'M-%'
													OR t.ref_no LIKE 'M-%')";
}

if (count($querystring_where) > 0) {
    $querystring_items .= ' WHERE '.implode(' AND ',$querystring_where);
}


// Gesamtanzahl der Suchergebnisse feststellen
$querystring_count = "SELECT COUNT(*) AS total FROM ($querystring_items) AS xyz";
$query_count = $dbi->connection->query($querystring_count);
$total_results = $query_count->fetch_object();
$dbi->setUserVar('total_results',$total_results->total);

// order-klausel
//$querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']} LIMIT ".($dbi->user['skip']).','.Z_LIST_ROWS_PAGE;
$querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']}";

// query ausführen
$query_items = $dbi->connection->query($querystring_items.$querystring_orderby);

// ausgabe der suchtermini
$suche_nach = array();
if (isset($_GET['cause_of_death']) && $_GET['cause_of_death']) $suche_nach[] = 'cause of death = executed';
if (isset($_GET['prisoner_of_war']) && $_GET['prisoner_of_war']) $suche_nach[] = 'imprisonment = prisoner of war';
if (isset($_GET['psychiatric_patient']) && $_GET['psychiatric_patient']) $suche_nach[] = 'imprisonment = psychiatric_patient';
if (isset($_GET['beddies_database']) && $_GET['beddies_database']) $suche_nach[] = 'source = Beddies Database';
if (isset($_GET['rothemund_list']) && $_GET['rothemund_list']) $suche_nach[] = 'source = Rothemund List';
if (isset($_GET['schlote_list']) && $_GET['schlote_list']) $suche_nach[] = 'source = Schlote List';
if (isset($_GET['m_series']) && $_GET['m_series']) $suche_nach[] = 'M-Series';
if (isset($_GET['ID_institution']) && $_GET['ID_institution']) {
	$institution = $dbi->connection->query('SELECT institution_name FROM nmv__institution WHERE ID_institution = '.$_GET['ID_institution'])->fetch_row();
	$suche_nach[] = 'institution = '.$institution[0];
}
if (isset($_GET['ID_tissue_institution']) && $_GET['ID_tissue_institution']){
	$tissue_institution = $dbi->connection->query('SELECT institution_name FROM nmv__institution WHERE ID_institution = '.$_GET['ID_tissue_institution'])->fetch_row();
	$suche_nach[] = 'tissue-institution = '.$tissue_institution[0];
}
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
        .$dbi->getListView('table_nmv_victims_details',$query_items)
        .'<div class="buttons">'
				.createButton (L_MODIFY_SEARCH,'javascript:history.back()','icon search')
        .createButton (L_NEW_SEARCH,'search.php','icon search')
        .'</div>'
	)
	//->set('sidebar','<h3>'.L_HELP.'</h3>'.$dbi->getTextblock_HTML ('results'))
	->cast();

?>
