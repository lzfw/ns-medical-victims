<?php
/**
* execute queries for variable victim search and show results
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
$exact_fields = array (	'ID_birth_country', 'nationality_1938');

// felder, die mit like gematcht werden (Trunkierung möglich, Diakritika distinkt, Basiszeichen ambivalent)
// --> If no diacritics are applied, it finds covers any combination: η would also return ἠ, ἦ or ἥ, while ἠ would find only ἠ.
$like_fields = array ();

//felder, die mit LIKE %xy% gematcht werden
$contain_fields = array();

// felder, die mit like ODER exakt gematcht werden (Trunkierung möglich, Diakritika indistinkt)
// --> Arabic vowel signs are treated indistinctively: سبب would also return سَبَبٌ, and vice versa.
$double_fields = array ();

// fields that trigger special conditions when ticked
$ticked_fields = array ();

//fields involving data from tables other than nmv__victim
//key defines table and column
$special_fields = array();

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

// make select-clauses part one
$querystring_items = '	SELECT DISTINCT v.ID_victim, v.surname, v.first_names,
																				v.birth_year, bc.english AS birth_country, v.birth_place, v.gender,
																				n.english AS nationality_1938, et.english AS ethnic_group,
                                        ve.exp_start_year, ve.experiment_duration, ve.exp_end_year, ve.ID_experiment,
                                        e.experiment_title, GROUP_CONCAT(DISTINCT inst.institution_name SEPARATOR "\n") AS institution_name, e.location_details
												FROM nmv__victim v
												LEFT JOIN nmv__country bc									ON bc.ID_country = v.ID_birth_country
												LEFT JOIN nmv__victim_experiment ve				ON v.ID_victim = ve.ID_victim
												LEFT JOIN nmv__experiment e 							ON ve.ID_experiment = e.ID_experiment
												LEFT JOIN nmv__experiment_institution ei 	ON ei.ID_experiment = ve.ID_experiment
                        LEFT JOIN nmv__institution inst    				ON inst.ID_institution = ei.ID_institution
												LEFT JOIN nmv__imprisoniation i						ON v.ID_victim = i.ID_victim
												LEFT JOIN nmv__nationality n        			ON n.ID_nationality = v.nationality_1938
												LEFT JOIN nmv__ethnicgroup et       			ON et.ID_ethnicgroup = v.ethnic_group
												LEFT JOIN nmv__med_history_brain b				ON v.ID_victim = b.ID_victim
												LEFT JOIN nmv__med_history_tissue t				ON v.ID_victim = t.ID_victim
												LEFT JOIN nmv__med_history_hosp h					ON v.ID_victim = h.ID_victim
												LEFT JOIN nmv__evaluation ev							ON v.ID_victim = ev.ID_victim
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
			$querystring_where[] = "v.$field LIKE '%".$filtered_field."%'";
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
			$querystring_where[] = "$key = '".getUrlParameter($field)."'";
    }
}
foreach ($special_contain_fields as $key=>$field) {
    if (getUrlParameter($field)) {
			$filtered_field = str_replace($filter_chars, $replace_chars, getUrlParameter($field));
			$querystring_where[] = "$key LIKE '%".$filtered_field."%'";
    }
}

//WHERE-CLAUSE zusammenführen
if (count($querystring_where) > 0) {
    $querystring_items .= ' WHERE '.implode(' AND ',$querystring_where);
		$querystring_items .= ' GROUP BY ve.ID_vict_exp, v.ID_victim';
}

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
if (isset($_GET['ID_birth_country']) && $_GET['ID_birth_country']) {
	$search_term = $dbi->connection->query('SELECT english FROM nmv__country WHERE ID_country = '.$_GET['ID_birth_country'])->fetch_row();
	$suche_nach[] = 'country of birth = '.$search_term[0];
}
if (isset($_GET['nationality_1938']) && $_GET['nationality_1938']) {
	$search_term = $dbi->connection->query('SELECT english  FROM nmv__nationality WHERE ID_nationality = '.$_GET['nationality_1938'])->fetch_row();
	$suche_nach[] = 'nationality in 1938 = '.$search_term[0];
}


// breadcrumbs
$dbi->addBreadcrumb (L_SEARCH,'search.php');

// layout
$layout
	->set('title','Results Victim-Experiment-Entries')
	->set('content',
        '<p>Search for: <em>'.implode(', ',$suche_nach).'</em><br>
				Number of results: '. $total_results->total. '</p>'
        .$dbi->getListView('table_nmv_vic_exp',$query_items)
        .'<div class="buttons">'
				.createButton (L_MODIFY_SEARCH,'javascript:history.back()','icon search')
        .createButton (L_NEW_SEARCH,'search.php','icon search')
        .'</div>'
	)
	//->set('sidebar','<h3>'.L_HELP.'</h3>'.$dbi->getTextblock_HTML ('results'))
	->cast();

?>
