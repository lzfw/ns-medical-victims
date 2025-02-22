<?php
/**
* execute queries for keyword search in diagnoses
*
*
*
*/

require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

// multi-dimensional $_GET to string for following fallback
$get_string = '';
foreach($_GET as $key => $value){
	if(is_array($value)):
		$get_string .= implode('', $value);
	else:
		$get_string .= $value;
	endif;
}
// fallback, if no parameters have been transmitted
if ($get_string == '') {
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
$exact_fields = array ();

// felder, die mit like gematcht werden (Trunkierung möglich, Diakritika distinkt, Basiszeichen ambivalent)
// --> If no diacritics are applied, it finds covers any combination: η would also return ἠ, ἦ or ἥ, while ἠ would find only ἠ.
$like_fields = array ();

//felder, die mit LIKE %xy% gematcht werden
$contain_fields = array('keyword');

// felder, die mit like ODER exakt gematcht werden (Trunkierung möglich, Diakritika indistinkt)
// --> Arabic vowel signs are treated indistinctively: سبب would also return سَبَبٌ, and vice versa.
$double_fields = array ();

// fields that trigger special conditions when ticked
$ticked_fields = array ();

//fields involving data from tables other than nmv__victim
//key defines table and column
$special_fields = array('dth.ID_diagnosis'			=> 'diagnosis_tag');

$special_contain_fields = array();

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
foreach ($special_fields as $field) {
	if (isset($_GET[$field]) && $_GET[$field] != '') $query[] = "$field={$_GET[$field]}";
}
foreach ($special_contain_fields as $field) {
	if (isset($_GET[$field]) && $_GET[$field] != '') $query[] = "$field={$_GET[$field]}";
}
$dbi->setUserVar('querystring',implode('&',$query));

// make select-clauses
$querystring_items = '	SELECT DISTINCT v.ID_victim, v.surname, v.first_names, v.birth_year,
															bc.country AS birth_country, n.nationality AS nationality_1938,
															et.ethnic_group, v.birth_place
												FROM nmv__victim v
												LEFT JOIN nmv__country bc									ON v.ID_birth_country = bc.ID_country
												LEFT JOIN nmv__nationality n 							ON v.ID_nationality_1938 = n.ID_nationality
												LEFT JOIN nmv__ethnic_group et						ON v.ID_ethnic_group = et.ID_ethnic_group
												LEFT JOIN nmv__victim_experiment ve				ON v.ID_victim = ve.ID_victim
												LEFT JOIN nmv__experiment e								ON ve.ID_experiment = e.ID_experiment
												LEFT JOIN nmv__med_history_brain b				ON v.ID_victim = b.ID_victim
												LEFT JOIN nmv__med_history_hosp h					ON v.ID_victim = h.ID_victim
												LEFT JOIN nmv__med_history_diagnosis d 					ON d.ID_victim = v.ID_victim
												LEFT JOIN nmv__diagnosis_brain db 				ON db.ID_med_history_brain = b.ID_med_history_brain
												LEFT JOIN nmv__diagnosis_hosp dh 					ON dh.ID_med_history_hosp = h.ID_med_history_hosp
												LEFT JOIN nmv__diagnosis_diagnosis dd  					ON dd.ID_med_history_diagnosis = d.ID_med_history_diagnosis    
                       							LEFT JOIN nmv__diagnosis_tag dth          ON dth.ID_diagnosis = db.ID_diagnosis OR dth.ID_diagnosis = dh.ID_diagnosis OR dth.ID_diagnosis = dd.ID_diagnosis
                        '; // für Ergebnisliste
$querystring_where = array(); // for where-part of select clause

//complete db d
if ($dbi->checkUserPermission('mpg')) :
	$querystring_where[] = 'v.mpg_project = -1';
endif;


// define MySQL-characterfilter (Trunkierungszeichen werden zu MySQL-Zeichen)
$filter_chars = array("'", '%', '_', '*', '٭');
$replace_chars = array('', ' ', ' ', '%', '%');

// WHERE Strings zusammenbauen

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
			$querystring_where[] = "v.cause_of_death LIKE '%".$filtered_field."%' OR
                              b.diagnosis LIKE '%".$filtered_field."%' OR
                              h.diagnosis LIKE '%".$filtered_field."%' OR
                              dth.diagnosis LIKE '%".$filtered_field."%' OR
                              h.notes LIKE '%".$filtered_field."%' 
                             ";
    }
}
foreach ($double_fields as $field) {
    if (getUrlParameter($field)) {
			$filtered_field = str_replace($filter_chars, $replace_chars, getUrlParameter($field));
			$querystring_where[] = "(v.$field LIKE '".$filtered_field."' OR v.$field = '".getUrlParameter($field)."')";
    }
}
foreach ($special_fields as $key=>$field) {
    if (getUrlParameter($field)) {
			$querystring_where[] = "$key = ".getUrlParameter($field)."";
    }
}
foreach ($special_contain_fields as $key=>$field) {
    if (getUrlParameter($field)) {
			$filtered_field = str_replace($filter_chars, $replace_chars, getUrlParameter($field));
			$querystring_where[] = "$key LIKE '%".$filtered_field."%'";
    }
}

// Add WHERE-clause and GROUP BY
$where_clause = '';
if (count($querystring_where) > 0) {
    $where_clause = ' WHERE '.implode(' AND ',$querystring_where);
		$where_clause_encoded = urlencode(utf8_encode($where_clause)); //encode for url-transfer to export
    $querystring_items .= $where_clause;
}
$querystring_items .= " GROUP BY v.ID_victim";

//Gesamtanzahl der Suchergebnisse feststellen
$querystring_count = "SELECT COUNT(*) AS total FROM ($querystring_items) AS xyz";
$query_count = $dbi->connection->query($querystring_count);
$total_results = $query_count->fetch_object();
$dbi->setUserVar('total_results',$total_results->total);

// order-klausel
$querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']}";

// query ausführen
$query_items = $dbi->connection->query($querystring_items.$querystring_orderby);

// ausgabe der suchtermini
$suche_nach = array();
if (isset($_GET['keyword']) && $_GET['keyword']) $suche_nach[] = 'victims with diagnoses containing: '.$_GET['keyword'];
if (isset($_GET['diagnosis_tag']) && $_GET['diagnosis_tag']) {
	$search_term = $dbi->connection->query('SELECT diagnosis FROM nmv__diagnosis_tag WHERE ID_diagnosis = '.$_GET['diagnosis_tag'])->fetch_row();
	$suche_nach[] = 'diagnosis tag = '.$search_term[0];
}



// breadcrumbs
$dbi->addBreadcrumb (L_SEARCH,'search.php');

// layout
$layout
	->set('title',L_RESULTS)
	->set('content',
        '<p>Search for: <em>'.implode(', AND ',$suche_nach).'</em><br>
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
