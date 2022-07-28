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
$exact_fields = array (	'twin', 					'mpg_project', 				'ID_birth_country',
												'birth_year',			'ID_dataset_origin', 	'ID_death_country',
												'death_year', 		'gender',							'religion',
												'ethnic_group', 	'nationality_1938', 	'ID_education',
												'occupation', 		'ID_arrest_country', 	'ID_perpetrator',
												'photo',					'nationality_after_1945');

// felder, die mit like gematcht werden (Trunkierung möglich, Diakritika distinkt, Basiszeichen ambivalent)
// --> If no diacritics are applied, it finds covers any combination: η would also return ἠ, ἦ or ἥ, while ἠ would find only ἠ.
$like_fields = array ();

//felder, die mit LIKE %xy% gematcht werden
$contain_fields = array('residence_after_1945_country', 'occupation_after_1945',
												'notes', 'notes_after_1945', 'notes_photo');

// felder, die mit like ODER exakt gematcht werden (Trunkierung möglich, Diakritika indistinkt)
// --> Arabic vowel signs are treated indistinctively: سبب would also return سَبَبٌ, and vice versa.
$double_fields = array ();

// fields that trigger special conditions when ticked
$ticked_fields = array ();

//fields involving data from tables other than nmv__victim
//key defines table and column
$special_fields = array('e.ID_experiment'			=> 'ID_experiment',
												'ei.ID_institution'   => 'exp_institution',
 												'i.ID_classification' => 'ID_classification',
												'i.location' 					=> 'location',
												't.ID_tissue_state' 	=> 'ID_tissue_state',
												't.ID_tissue_form' 		=> 'ID_tissue_form',
												'b.brain_report_year' => 'brain_report_year',
												'b.ID_institution'		=> 'brain_report_institution',
												'db.ID_diagnosis'			=> 'brain_report_ID_diagnosis',
												'h.date_entry_year' 	=> 'hospitalisation_year',
												'h.ID_institution'		=> 'hospitalisation_institution',
												'dh.ID_diagnosis'			=> 'hospitalisation_ID_diagnosis',
												'ev.evaluation_status'=> 'evaluation_status',
												't.ID_institution'   	=> 'tissue_institution',
												'ef.ID_foi'						=> 'ID_foi'
												);

$special_contain_fields = array('CONCAT(IFNULL(b.diagnosis, ""), IFNULL(dtb.diagnosis, ""))'	=> 'brain_report_diagnosis',
																'CONCAT(IFNULL(h.diagnosis, ""), IFNULL(dth.diagnosis, ""))'	=> 'hospitalisation_diagnosis',
																'b.ref_no'            => 'ref_no_brain',
																't.ref_no'						=> 'ref_no_tissue',
																'h.autopsy_ref_no'		=> 'autopsy_ref_no',
																);

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
if ((isset($_GET['ID_experiment']) && ($_GET['ID_experiment'])) || (isset($_GET['exp_institution']) && ($_GET['exp_institution']))): // query for experiment-related filters: shows and links victim-experiment data
	$querystring_items = '	SELECT DISTINCT v.ID_victim, v.surname, v.first_names,
																					v.birth_year, bc.english AS birth_country, v.birth_place,
																					n.english AS nationality_1938, et.english AS ethnic_group,
																					ve.exp_start_day, ve.exp_start_month, ve.exp_start_year,
																					s.english AS survival, ve.ID_vict_exp
													FROM nmv__victim v
													LEFT JOIN nmv__country bc 								ON bc.ID_country = v.ID_birth_country
													LEFT JOIN nmv__victim_experiment ve				ON v.ID_victim = ve.ID_victim
													LEFT JOIN nmv__survival s 								ON s.ID_survival = ve.ID_survival
													LEFT JOIN nmv__experiment e 							ON ve.ID_experiment = e.ID_experiment
													LEFT JOIN nmv__experiment_institution ei 	ON ei.ID_experiment = e.ID_experiment
													LEFT JOIN nmv__experiment_foi ef					ON ef.ID_experiment = e.ID_experiment
													LEFT JOIN nmv__field_of_interest foi			ON foi.ID_foi = ef.ID_foi
													LEFT JOIN nmv__imprisoniation i						ON v.ID_victim = i.ID_victim
													LEFT JOIN nmv__nationality n        			ON n.ID_nationality = v.nationality_1938
													LEFT JOIN nmv__ethnicgroup et       			ON et.ID_ethnicgroup = v.ethnic_group
													LEFT JOIN nmv__med_history_brain b				ON v.ID_victim = b.ID_victim
													LEFT JOIN nmv__diagnosis_brain db 				ON db.ID_med_history_brain = b.ID_med_history_brain
													LEFT JOIN nmv__diagnosis_tag dtb					ON dtb.ID_diagnosis = db.ID_diagnosis
													LEFT JOIN nmv__med_history_tissue t				ON v.ID_victim = t.ID_victim
													LEFT JOIN nmv__med_history_hosp h					ON v.ID_victim = h.ID_victim
													LEFT JOIN nmv__diagnosis_hosp dh					ON dh.ID_med_history_hosp = h.ID_med_history_hosp
													LEFT JOIN nmv__diagnosis_tag dth					ON dth.ID_diagnosis = dh.ID_diagnosis
													LEFT JOIN nmv__evaluation ev							ON v.ID_victim = ev.ID_victim
													LEFT JOIN nmv__victim_source vs 					ON vs.ID_victim = v.ID_victim
													LEFT JOIN nmv__victim_literature vl 			ON vl.ID_victim = v.ID_victim
												'; // für Ergebnisliste
else:  // default query
		$querystring_items = '	SELECT DISTINCT v.ID_victim, v.surname, v.first_names,
																					v.birth_year, bc.english AS birth_country, v.birth_place,
																					n.english AS nationality_1938, et.english AS ethnic_group
													FROM nmv__victim v
													LEFT JOIN nmv__country bc 						ON bc.ID_country = v.ID_birth_country
													LEFT JOIN nmv__victim_experiment ve		ON v.ID_victim = ve.ID_victim
													LEFT JOIN nmv__survival s 						ON s.ID_survival = ve.ID_survival
													LEFT JOIN nmv__experiment e 					ON ve.ID_experiment = e.ID_experiment
													LEFT JOIN nmv__experiment_foi ef			ON ef.ID_experiment = e.ID_experiment
													LEFT JOIN nmv__field_of_interest foi	ON foi.ID_foi = ef.ID_foi
													LEFT JOIN nmv__imprisoniation i				ON v.ID_victim = i.ID_victim
													LEFT JOIN nmv__nationality n        	ON n.ID_nationality = v.nationality_1938
													LEFT JOIN nmv__ethnicgroup et       	ON et.ID_ethnicgroup = v.ethnic_group
													LEFT JOIN nmv__med_history_brain b		ON v.ID_victim = b.ID_victim
													LEFT JOIN nmv__diagnosis_brain db 		ON db.ID_med_history_brain = b.ID_med_history_brain
													LEFT JOIN nmv__diagnosis_tag dtb			ON dtb.ID_diagnosis = db.ID_diagnosis
													LEFT JOIN nmv__med_history_tissue t		ON v.ID_victim = t.ID_victim
													LEFT JOIN nmv__med_history_hosp h			ON v.ID_victim = h.ID_victim
													LEFT JOIN nmv__diagnosis_hosp dh			ON dh.ID_med_history_hosp = h.ID_med_history_hosp
													LEFT JOIN nmv__diagnosis_tag dth			ON dth.ID_diagnosis = dh.ID_diagnosis
													LEFT JOIN nmv__evaluation ev					ON v.ID_victim = ev.ID_victim
													LEFT JOIN nmv__victim_source vs 			ON vs.ID_victim = v.ID_victim
													LEFT JOIN nmv__victim_literature vl 	ON vl.ID_victim = v.ID_victim
												'; // für Ergebnisliste}
endif;
$querystring_where = array(); // for where-part of select clause
$querystring_where[] = "was_prisoner_assistant != 'prisoner assistant only'";  

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
			if ($field == 'photo') {
				$querystring_where[] = "(vs.source_has_photo = -1 OR
																 vl.literature_has_photo = -1 OR
																 b.brain_report_has_photo = -1 OR
																 h.hosp_has_photo = -1 OR
																 v.photo_exists = -1)";
			} else {
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
if (isset($_GET['birth_year']) && $_GET['birth_year']) $suche_nach[] = 'year of birth = '.$_GET['birth_year'];
if (isset($_GET['twin']) && $_GET['twin']) $suche_nach[] = 'twins only';
if (isset($_GET['ID_death_country']) && $_GET['ID_death_country'])  {
	$search_term = $dbi->connection->query('SELECT english FROM nmv__country WHERE ID_country = '.$_GET['ID_death_country'])->fetch_row();
	$suche_nach[] = 'country of death = '.$search_term[0];
}
if (isset($_GET['death_year']) && $_GET['death_year']) $suche_nach[] = 'year of death = '.$_GET['death_year'];
if (isset($_GET['gender']) && $_GET['gender']) $suche_nach[] = 'gender = '.$_GET['gender'];
if (isset($_GET['religion']) && $_GET['religion']) {
	$search_term = $dbi->connection->query('SELECT english FROM nmv__religion WHERE ID_religion = '.$_GET['religion'])->fetch_row();
	$suche_nach[] = 'religion = '.$search_term[0];
}
if (isset($_GET['ethnic_group']) && $_GET['ethnic_group']) {
	$search_term = $dbi->connection->query('SELECT english FROM nmv__ethnicgroup WHERE ID_ethnicgroup = '.$_GET['ethnic_group'])->fetch_row();
	$suche_nach[] = 'ethnic group = '.$search_term[0];
}
if (isset($_GET['nationality_1938']) && $_GET['nationality_1938']) {
	$search_term = $dbi->connection->query('SELECT english  FROM nmv__nationality WHERE ID_nationality = '.$_GET['nationality_1938'])->fetch_row();
	$suche_nach[] = 'nationality in 1938 = '.$search_term[0];
}
if (isset($_GET['ID_education']) && $_GET['ID_education']) {
	$search_term = $dbi->connection->query('SELECT english FROM nmv__education WHERE ID_education = '.$_GET['ID_education'])->fetch_row();
	$suche_nach[] = 'education = '.$search_term[0];
}
if (isset($_GET['occupation']) && $_GET['occupation']) {
	$search_term = $dbi->connection->query('SELECT english FROM nmv__occupation WHERE ID_occupation = '.$_GET['occupation'])->fetch_row();
	$suche_nach[] = 'occupation = '.$search_term[0];
}
if (isset($_GET['ID_arrest_country']) && $_GET['ID_arrest_country']) {
	$search_term = $dbi->connection->query('SELECT english FROM nmv__country WHERE ID_country = '.$_GET['ID_arrest_country'])->fetch_row();
	$suche_nach[] = 'country of arrest = '.$search_term[0];
}
if (isset($_GET['ID_experiment']) && $_GET['ID_experiment']) {
	$search_term = $dbi->connection->query('SELECT experiment_title FROM nmv__experiment WHERE ID_experiment = '.$_GET['ID_experiment'])->fetch_row();
	$suche_nach[] = 'title of experiment = '.$search_term[0];
}
if (isset($_GET['ID_foi']) && $_GET['ID_foi']) {
	$search_term = $dbi->connection->query('SELECT english FROM nmv__field_of_interest WHERE ID_foi = '.$_GET['ID_foi'])->fetch_row();
	$suche_nach[] = 'field of interest = '.$search_term[0];
}
if (isset($_GET['exp_institution']) && $_GET['exp_institution']) {
	$search_term = $dbi->connection->query('SELECT institution_name FROM nmv__institution WHERE ID_institution = '.$_GET['exp_institution'])->fetch_row();
	$suche_nach[] = 'institution of experiment = '.$search_term[0];
}
if (isset($_GET['ID_classification']) && $_GET['ID_classification']) {
	$search_term = $dbi->connection->query('SELECT english FROM nmv__victim_classification WHERE ID_classification = '.$_GET['ID_classification'])->fetch_row();
	$suche_nach[] = 'imprisonment classification = '.$search_term[0];
}
if (isset($_GET['location']) && $_GET['location']) $suche_nach[] = 'imprisonment location = '.$_GET['location'];

if (isset($_GET['mpg_project']) && $_GET['mpg_project']) $suche_nach[] = 'mpg_project only';
if (isset($_GET['ID_dataset_origin']) && $_GET['ID_dataset_origin']) {
	$search_term = $dbi->connection->query('SELECT work_group FROM nmv__dataset_origin WHERE ID_dataset_origin = '.$_GET['ID_dataset_origin'])->fetch_row();
	$suche_nach[] = 'workgroup(s) = '.$search_term[0];
}
if (isset($_GET['tissue_institution']) && $_GET['tissue_institution']) {
	$search_term = $dbi->connection->query('SELECT institution_name FROM nmv__institution WHERE ID_institution = '.$_GET['tissue_institution'])->fetch_row();
	$suche_nach[] = 'location of tissue = '.$search_term[0];
}
if (isset($_GET['ID_tissue_form']) && $_GET['ID_tissue_form']) {
	$search_term = $dbi->connection->query('SELECT english FROM nmv__tissue_form WHERE ID_tissue_form = '.$_GET['ID_tissue_form'])->fetch_row();
	$suche_nach[] = 'form of tissue = '.$search_term[0];
}
if (isset($_GET['ID_tissue_state']) && $_GET['ID_tissue_state']) {
	$search_term = $dbi->connection->query('SELECT english FROM nmv__tissue_state WHERE ID_tissue_state = '.$_GET['ID_tissue_state'])->fetch_row();
	$suche_nach[] = 'form of state = '.$search_term[0];
}
if (isset($_GET['ref_no_tissue']) && $_GET['ref_no_tissue']) $suche_nach[] = 'RefNo Tissue contains:  '.$_GET['ref_no_tissue'];
if (isset($_GET['brain_report_year']) && $_GET['brain_report_year']) $suche_nach[] = 'year of brain report = '.$_GET['brain_report_year'];
if (isset($_GET['brain_report_institution']) && $_GET['brain_report_institution']) {
	$search_term = $dbi->connection->query('SELECT institution_name FROM nmv__institution WHERE ID_institution = '.$_GET['brain_report_institution'])->fetch_row();
	$suche_nach[] = 'institution of brain report = '.$search_term[0];
}
if (isset($_GET['brain_report_ID_diagnosis']) && $_GET['brain_report_ID_diagnosis']) {
	$search_term = $dbi->connection->query('SELECT diagnosis FROM nmv__diagnosis_tag WHERE ID_diagnosis = '.$_GET['brain_report_ID_diagnosis'])->fetch_row();
	$suche_nach[] = 'diagnosis tag brain report = '.$search_term[0];
}
if (isset($_GET['brain_report_diagnosis']) && $_GET['brain_report_diagnosis']) $suche_nach[] = 'diagnosis from brain report contains: '.$_GET['brain_report_diagnosis'];
if (isset($_GET['hospitalisation_ID_diagnosis']) && $_GET['hospitalisation_ID_diagnosis']) {
	$search_term = $dbi->connection->query('SELECT diagnosis FROM nmv__diagnosis_tag WHERE ID_diagnosis = '.$_GET['hospitalisation_ID_diagnosis'])->fetch_row();
	$suche_nach[] = 'diagnosis tag hospitalisation = '.$search_term[0];
}
if (isset($_GET['ref_no_brain']) && $_GET['ref_no_brain']) $suche_nach[] = 'RefNo Brain report contains:  '.$_GET['ref_no_brain'];

if (isset($_GET['hospitalisation_year']) && $_GET['hospitalisation_year']) $suche_nach[] = 'year of hospitalisation = '.$_GET['hospitalisation_year'];
if (isset($_GET['hospitalisation_institution']) && $_GET['hospitalisation_institution']) {
	$search_term = $dbi->connection->query('SELECT institution_name FROM nmv__institution WHERE ID_institution = '.$_GET['hospitalisation_institution'])->fetch_row();
	$suche_nach[] = 'institution of hospitalisation = '.$search_term[0];
}
if (isset($_GET['hospitalisation_diagnosis']) && $_GET['hospitalisation_diagnosis']) $suche_nach[] = 'hospitalisation diagnosis contains: '.$_GET['hospitalisation_diagnosis'];
if (isset($_GET['evaluation_status']) && $_GET['evaluation_status']) {
	$search_term = $dbi->connection->query('SELECT english FROM nmv__victim_evaluation_status WHERE ID_status = '.$_GET['evaluation_status'])->fetch_row();
	$suche_nach[] = 'evaluation status = '.$search_term[0];
}
if (isset($_GET['autopsy_ref_no']) && $_GET['autopsy_ref_no']) $suche_nach[] = 'AutopsyRefNo Hospitalisation contains:  '.$_GET['autopsy_ref_no'];

if (isset($_GET['residence_after_1945_country']) && $_GET['residence_after_1945_country']) $suche_nach[] = 'residence after 1945 (country) = '.$_GET['residence_after_1945_country'];
if (isset($_GET['occupation_after_1945']) && $_GET['occupation_after_1945']) $suche_nach[] = 'occupation after 1945 = '.$_GET['occupation_after_1945'];
if (isset($_GET['nationality_after_1945']) && $_GET['nationality_after_1945']) {
	$search_term = $dbi->connection->query('SELECT english FROM nmv__nationality WHERE ID_nationality = '.$_GET['nationality_after_1945'])->fetch_row();
	$suche_nach[] = 'nationality after 1945 = '.$search_term[0];
}
if (isset($_GET['notes']) && $_GET['notes']) $suche_nach[] = 'notes = ... '.$_GET['notes'] . ' ...';
if (isset($_GET['notes_after_1945']) && $_GET['notes_after_1945']) $suche_nach[] = 'notes after 1945 = ... ' . $_GET['notes_after_1945'] . ' ...';
if (isset($_GET['photo']) && $_GET['photo']) $suche_nach[] = 'photo contained';


// breadcrumbs
$dbi->addBreadcrumb (L_SEARCH,'search.php');

// layout
if((isset($_GET['ID_experiment']) && $_GET['ID_experiment']) || (isset($_GET['exp_institution']) && $_GET['exp_institution'])) { // special table for experiment-related filters: shows and links victim-experiment data

	$layout
		->set('title',L_RESULTS)
		->set('content',
	        '<p>Search for: <em>'.implode(' AND ', $suche_nach).'</em><br>
					Number of results: '. $total_results->total. '</p>'
	        . $dbi->getListView('table_nmv_victims_exp',$query_items)
	        .'<div class="buttons">'
					.createButton (L_MODIFY_SEARCH,'javascript:history.back()','icon search')
	        .createButton (L_NEW_SEARCH,'search.php','icon search')
	        .'</div>'
		)
		//->set('sidebar','<h3>'.L_HELP.'</h3>'.$dbi->getTextblock_HTML ('results'))
		->cast();
} else {// default table
	$layout
		->set('title',L_RESULTS)
		->set('content',
	        '<p>Search for: <em>'.implode(', AND ',$suche_nach).'</em><br>
					Number of results: '. $total_results->total. '</p>'
	        . $dbi->getListView('table_nmv_victims_details',$query_items)
	        .'<div class="buttons">'
					.createButton (L_MODIFY_SEARCH,'javascript:history.back()','icon search')
	        .createButton (L_NEW_SEARCH,'search.php','icon search')
	        .'</div>'
		)
		//->set('sidebar','<h3>'.L_HELP.'</h3>'.$dbi->getTextblock_HTML ('results'))
		->cast();
}

?>
