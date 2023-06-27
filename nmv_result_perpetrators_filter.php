<?php
/**
* execute queries for variable perpetrator search (filter) and show results
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
$exact_fields = array (	'birth_year',				'death_year',					'gender',
												'ID_religion', 			'ID_nationality_1938',
												'ID_perp_class',		'nsdap_member', 			'ss_member',
												'sa_member', 				'other_nsdap_organisations_member',
												'ID_birth_country', 'ID_death_country',		'leopoldina_member',
												'mpg_project'
											);

// felder, die mit like gematcht werden (Trunkierung möglich, Diakritika distinkt, Basiszeichen ambivalent)
// --> If no diacritics are applied, it finds covers any combination: η would also return ἠ, ἦ or ἥ, while ἠ would find only ἠ.
$like_fields = array ();

//felder, die mit LIKE %xy% gematcht werden
$contain_fields = array(	'career_history',					'details_all_memberships',	'prosecution',
													'prison_time',						'career_after_1945',				'notes',
													'titles',									'occupation'
												);

// felder, die mit like ODER exakt gematcht werden (Trunkierung möglich, Diakritika indistinkt)
// --> Arabic vowel signs are treated indistinctively: سبب would also return سَبَبٌ, and vice versa.
$double_fields = array ();

// fields that trigger special conditions when ticked
$ticked_fields = array ();

//fields involving data from tables other than nmv__victim
//key defines table and column
$special_fields = array( 	'q.qualification_year'		=> 'qualification_year');

$special_contain_fields = array(	'q.qualification_place'		=>	'qualification_place',
																	'q.thesis_title'					=>	'thesis_title');


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
$querystring_items = '	SELECT p.ID_perpetrator, p.surname, p.first_names, p.birth_year, p.death_year, bc.country AS birth_country,
												p.birth_place, p.occupation
												FROM nmv__perpetrator p
												LEFT JOIN nmv__country bc ON bc.ID_country = p.ID_birth_country
												LEFT JOIN nmv__qualification q ON q.ID_perpetrator = p.ID_perpetrator'; // für Ergebnisliste
$querystring_where = array(); // for where-part of select clause


// define MySQL-characterfilter (Trunkierungszeichen werden zu MySQL-Zeichen)
$filter_chars = array("'", '%', '_', '*', '٭');
$replace_chars = array('', ' ', ' ', '%', '%');

// WHERE Strings zusammenbauen

foreach ($exact_fields as $field) {
    if (getUrlParameter($field)) :
			if (getUrlParameter($field) == 'NULL') :
				$querystring_where[] = "p.$field IS NULL";
			else:
        $querystring_where[] = "p.$field = '".getUrlParameter($field)."'";
			endif;
    endif;
}
foreach ($like_fields as $field) {
    if (getUrlParameter($field)) {
			$filtered_field = str_replace($filter_chars, $replace_chars, getUrlParameter($field));
			$querystring_where[] = "TRIM(p.$field) LIKE TRIM('".$filtered_field."')";
    }
}
foreach ($contain_fields as $field) {
    if (getUrlParameter($field)) {
			$filtered_field = str_replace($filter_chars, $replace_chars, getUrlParameter($field));
			$querystring_where[] = "p.$field LIKE '%".$filtered_field."%'";
    }
}
foreach ($double_fields as $field) {
    if (getUrlParameter($field)) {
			$filtered_field = str_replace($filter_chars, $replace_chars, getUrlParameter($field));
			$querystring_where[] = "(p.$field LIKE '".$filtered_field."' OR p.$field = '".getUrlParameter($field)."')";
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
//customized queries
if (getUrlParameter('prison_time-info')) {
	$filtered_field = str_replace($filter_chars, $replace_chars, getUrlParameter('prison_time-info'));
	$querystring_where[] = "(p.prison_time IS NOT NULL)";
}
if (getUrlParameter('prosecution-info')) {
	$filtered_field = str_replace($filter_chars, $replace_chars, getUrlParameter('prosecution-info'));
	$querystring_where[] = "(p.prosecution IS NOT NULL)";
}
if (getUrlParameter('freetext-fields')) {
	$filtered_field = str_replace($filter_chars, $replace_chars, getUrlParameter('freetext-fields'));
	$querystring_where[] = "(CONCAT(IFNULL(p.occupation, ''), ' ', IFNULL(p.career_history, ''), ' ', IFNULL(p.details_all_memberships, ''),
	 																' ', IFNULL(p.career_after_1945, ''), ' ', IFNULL(p.prosecution, ''), ' ', IFNULL(p.prison_time, ''),
																	' ', IFNULL(p.notes, ''), ' ') LIKE '%".$filtered_field."%')";
}
if (getUrlParameter('died_before_end_of_war')) {
	$filtered_field = str_replace($filter_chars, $replace_chars, getUrlParameter('died_before_end_of_war'));
	$querystring_where[] = "(p.death_year BETWEEN 1933 AND 1945 AND p.death_month BETWEEN 1 AND 5)";
	// $querystring_where[] = "(p.death_year = 1956 AND p.death_month = 6)";
}


// Add WHERE-clause and GROUP BY
$where_clause = '';
if (count($querystring_where) > 0) {
    $where_clause = ' WHERE '.implode(' AND ',$querystring_where);
		$where_clause_encoded = urlencode(utf8_encode($where_clause)); //encode for url-transfer to export
    $querystring_items .= $where_clause;
}
$querystring_items .= " GROUP BY p.ID_perpetrator";
// echo $querystring_items;

//Gesamtanzahl der Suchergebnisse feststellen
$querystring_count = "SELECT COUNT(*) AS total FROM ($querystring_items) AS xyz";
$query_count = $dbi->connection->query($querystring_count);
$total_results = $query_count->fetch_object();
$dbi->setUserVar('total_results',$total_results->total);

// order-klausel
$querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']}";

// query ausführen
$query_items = $dbi->connection->query($querystring_items.$querystring_orderby);

// output of search expressions
$suche_nach = array();
if (isset($_GET['ID_birth_country']) && $_GET['ID_birth_country']) {
	$search_term = $dbi->connection->query('SELECT country FROM nmv__country WHERE ID_country = '.$_GET['ID_birth_country'])->fetch_row();
	$suche_nach[] = 'country of birth = '.$search_term[0];
}if (isset($_GET['birth_year']) && $_GET['birth_year']) $suche_nach[] = 'year of birth = '.$_GET['birth_year'];
if (isset($_GET['ID_death_country']) && $_GET['ID_death_country'])  {
	$search_term = $dbi->connection->query('SELECT country FROM nmv__country WHERE ID_country = '.$_GET['ID_death_country'])->fetch_row();
	$suche_nach[] = 'country of death = '.$search_term[0];
}if (isset($_GET['death_year']) && $_GET['death_year']) $suche_nach[] = 'year of death = '.$_GET['death_year'];
if (isset($_GET['gender']) && $_GET['gender']) $suche_nach[] = 'gender = '.$_GET['gender'];
if (isset($_GET['ID_religion']) && $_GET['ID_religion']) {
	$search_term = $dbi->connection->query('SELECT religion FROM nmv__religion WHERE ID_religion = '.$_GET['ID_religion'])->fetch_row();
	$suche_nach[] = 'religion = '.$search_term[0];
}
if (isset($_GET['ID_nationality_1938']) && $_GET['ID_nationality_1938']) {
	$search_term = $dbi->connection->query('SELECT nationality  FROM nmv__nationality WHERE ID_nationality = '.$_GET['ID_nationality_1938'])->fetch_row();
	$suche_nach[] = 'nationality in 1938 = '.$search_term[0];
}
if (isset($_GET['titles']) && $_GET['titles']) $suche_nach[] = 'titles = '.$_GET['titles'];
if (isset($_GET['qualification_place']) && $_GET['qualification_place']) $suche_nach[] = 'place of qualification = '.$_GET['qualification_place'];
if (isset($_GET['qualification_year']) && $_GET['qualification_year']) $suche_nach[] = 'year of qualification = '.$_GET['qualification_year'];
if (isset($_GET['thesis_title']) && $_GET['thesis_title']) $suche_nach[] = 'thesis title = '.$_GET['thesis_title'];
if (isset($_GET['occupation']) && $_GET['occupation']) $suche_nach[] = 'occupation = '.$_GET['occupation'];
if (isset($_GET['ID_perp_class']) && $_GET['ID_perp_class']) {
	$search_term = $dbi->connection->query('SELECT classification FROM nmv__perpetrator_classification WHERE ID_perp_class = '.$_GET['ID_perp_class'])->fetch_row();
	$suche_nach[] = 'classification = '.$search_term[0];
}
if (isset($_GET['career_history']) && $_GET['career_history']) $suche_nach[] = 'career history = '.$_GET['career_history'];
if (isset($_GET['leopoldina_member']) && $_GET['leopoldina_member']) $suche_nach[] = 'leopoldina members';
if (isset($_GET['nsdap_member']) && $_GET['nsdap_member']) $suche_nach[] = 'nsdap members';
if (isset($_GET['ss_member']) && $_GET['ss_member']) $suche_nach[] = 'ss members';
if (isset($_GET['sa_member']) && $_GET['sa_member']) $suche_nach[] = 'sa members';
if (isset($_GET['other_nsdap_organisations_member']) && $_GET['other_nsdap_organisations_member']) $suche_nach[] = 'other nsdap organisations';
if (isset($_GET['details_all_memberships']) && $_GET['details_all_memberships']) $suche_nach[] = 'details memberships = '.$_GET['details_all_memberships'];
if (isset($_GET['prosecution']) && $_GET['prosecution']) $suche_nach[] = 'prosecution = '.$_GET['prosecution'];
if (isset($_GET['prison_time']) && $_GET['prison_time']) $suche_nach[] = 'prison time = '.$_GET['prison_time'];
if (isset($_GET['career_after_1945']) && $_GET['career_after_1945']) $suche_nach[] = 'career after 1945 = '.$_GET['career_after_1945'];
if (isset($_GET['notes']) && $_GET['notes']) $suche_nach[] = 'notes = '.$_GET['notes'];

if (isset($_GET['prosecution-info']) && $_GET['prosecution-info']) $suche_nach[] = 'perpetrators with information about prosecution';
if (isset($_GET['prison_time-info']) && $_GET['prison_time-info']) $suche_nach[] = 'perpetrators with information about prison time';
if (isset($_GET['died_before_end_of_war']) && $_GET['died_before_end_of_war']) $suche_nach[] = 'perpetrators who died before June 1945';
if (isset($_GET['freetext-fields']) && $_GET['freetext-fields']) $suche_nach[] = 'keyword search in freetext-fields for: '.$_GET['freetext-fields'];
if (isset($_GET['mpg_project']) && $_GET['mpg_project']) $suche_nach[] = 'perpetrators relevant for MPG-Project';

// breadcrumbs
$dbi->addBreadcrumb (L_SEARCH,'search.php');

// layout
$layout
	->set('title', 'Results Perpetrators Filter')
	->set('content',
        '<p>Search for: <em>'.implode(', ',$suche_nach).'</em><br>
				Number of results: ' . $total_results->total . '</p>'
				. '<div class="buttons">'.createButton ('Export Table to .csv',"nmv_export.php?type=csv&entity=perpetrator&where-clause=$where_clause_encoded",'icon download')
																 .createButton ('Export Table to .xls',"nmv_export.php?type=xls&entity=perpetrator&where-clause=$where_clause_encoded",'icon download')
				. '</div>'
        .$dbi->getListView('table_nmv_perpetrators',$query_items)
        .'<div class="buttons">'
				.createButton (L_MODIFY_SEARCH,'javascript:history.back()','icon search')
        .createButton (L_NEW_SEARCH,'search.php','icon search')
        .'</div>'
	)
	//->set('sidebar','<h3>'.L_HELP.'</h3>'.$dbi->getTextblock_HTML ('results'))
	->cast();

?>
