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
$exact_fields = array ('twin', 'mpg_project', 'ID_birth_country', 'birth_year', 'ID_dataset_origin', 'ID_death_country',
	'death_year', 'gender',	'ID_religion', 'ID_ethnic_group', 'ID_nationality_1938', 'ID_education', 'ID_occupation',
	'ID_arrest_country', 'ID_perpetrator', 'photo',	 'stolperstein_exists', 'ID_nationality_after_1945', 'ID_death_institution',
	'ID_evaluation_status', 'compensation', 'entry_status', 'potential_old_profile', 'clinical_ID_diagnosis');

// felder, die mit like gematcht werden (Trunkierung möglich, Diakritika distinkt, Basiszeichen ambivalent)
// --> If no diacritics are applied, it finds covers any combination: η would also return ἠ, ἦ or ἥ, while ἠ would find only ἠ.
$like_fields = array ();

//felder, die mit LIKE %xy% gematcht werden
$contain_fields = array('residence_after_1945_country', 'occupation_after_1945', 'notes', 'notes_after_1945',
	'notes_photo', 'birth_place', 'death_place', 'arrest_history', 'internal_notes');

// felder, die mit like ODER exakt gematcht werden (Trunkierung möglich, Diakritika indistinkt)
// --> Arabic vowel signs are treated indistinctively: سبب would also return سَبَبٌ, and vice versa.
$double_fields = array ();

// fields that trigger special conditions when ticked
$ticked_fields = array ();

//fields involving data from tables other than nmv__victim
//key defines table and column
$special_fields = array('ex.ID_experiment'		=> 'ID_experiment',
						'ei.ID_institution'   	=> 'exp_institution',
						'ic.ID_classification' 	=> 'ID_classification',
						'i.location' 			=> 'location',
						't.ID_tissue_state' 	=> 'ID_tissue_state',
						't.ID_tissue_form' 		=> 'ID_tissue_form',
						'b.brain_report_year' 	=> 'brain_report_year',
						'b.ID_institution'		=> 'brain_report_institution',
						'db.ID_diagnosis'		=> 'brain_report_ID_diagnosis',
						'h.date_entry_year' 	=> 'hospitalisation_year',
						'h.ID_institution'		=> 'hospitalisation_institution',
						't.ID_institution'   	=> 'tissue_institution',
						'ef.ID_foi'				=> 'ID_foi',
						'i.ID_institution'    	=> 'ID_imprisonment_institution'
						);

$special_contain_fields = array("CONCAT(IFNULL(b.diagnosis, ''), IFNULL(dtb.diagnosis, ''))" => 'brain_report_diagnosis',
	"CONCAT(IFNULL(h.diagnosis, ''), IFNULL(dth.diagnosis, ''), IFNULL(d.diagnosis, ''), IFNULL(dtd.diagnosis, ''))" => 'clinical_diagnosis',
	"CONCAT(IFNULL(t.ref_no, ''), ' ', IFNULL(t.ref_no_2, ''))"    => 'ref_no_tissue',
	'h.autopsy_ref_no'	=> 'autopsy_ref_no',
	'b.ref_no'			=> 'ref_no_brain',
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
if ((isset($_GET['ID_experiment']) && ($_GET['ID_experiment'])) || (isset($_GET['exp_institution']) && ($_GET['exp_institution']))):
// query for experiment-related filters: shows and links victim-experiment data
	$querystring_items = '	
		SELECT DISTINCT v.ID_victim, v.surname, v.first_names, v.birth_year, bc.country AS birth_country, v.birth_place, 
                n.nationality AS nationality_1938, et.ethnic_group, ve.exp_start_day, ve.exp_start_month, 
                ve.exp_start_year, s.survival, ve.ID_vict_exp
		FROM nmv__victim v
		LEFT JOIN nmv__country bc 								ON bc.ID_country = v.ID_birth_country
		LEFT JOIN nmv__victim_experiment ve						ON v.ID_victim = ve.ID_victim
		LEFT JOIN nmv__survival s 								ON s.ID_survival = ve.ID_survival
		LEFT JOIN nmv__experiment ex 							ON ve.ID_experiment = ex.ID_experiment
		LEFT JOIN nmv__experiment_institution ei 				ON ei.ID_experiment = ex.ID_experiment
		LEFT JOIN nmv__experiment_foi ef						ON ef.ID_experiment = ex.ID_experiment
		LEFT JOIN nmv__field_of_interest foi					ON foi.ID_foi = ef.ID_foi
		LEFT JOIN nmv__imprisonment i							ON v.ID_victim = i.ID_victim
		LEFT JOIN nmv__imprisonment_classification ic 			ON ic.ID_imprisonment = i.ID_imprisonment
		LEFT JOIN nmv__nationality n        					ON n.ID_nationality = v.ID_nationality_1938
		LEFT JOIN nmv__ethnic_group et       					ON et.ID_ethnic_group = v.ID_ethnic_group
		LEFT JOIN nmv__med_history_brain b						ON v.ID_victim = b.ID_victim
		LEFT JOIN nmv__diagnosis_brain db 						ON db.ID_med_history_brain = b.ID_med_history_brain
		LEFT JOIN nmv__diagnosis_tag dtb						ON dtb.ID_diagnosis = db.ID_diagnosis
		LEFT JOIN nmv__med_history_tissue t						ON v.ID_victim = t.ID_victim
		LEFT JOIN nmv__med_history_hosp h						ON v.ID_victim = h.ID_victim
		LEFT JOIN nmv__diagnosis_hosp dh						ON dh.ID_med_history_hosp = h.ID_med_history_hosp
		LEFT JOIN nmv__diagnosis_tag dth						ON dth.ID_diagnosis = dh.ID_diagnosis
		LEFT JOIN nmv__med_history_diagnosis d					ON d.ID_victim = v.ID_victim
		LEFT JOIN nmv__diagnosis_diagnosis dd 					ON dd.ID_med_history_diagnosis = d.ID_med_history_diagnosis
		LEFT JOIN nmv__diagnosis_tag dtd 						ON dtd.ID_diagnosis = dd.ID_diagnosis
		LEFT JOIN nmv__victim_source vs 						ON vs.ID_victim = v.ID_victim
		LEFT JOIN nmv__victim_literature vl 					ON vl.ID_victim = v.ID_victim
		LEFT JOIN nmv__institution di								ON di.ID_institution = v.ID_death_institution
		'; // für Ergebnisliste
else:  // default query
	$querystring_items = '	
		SELECT DISTINCT v.ID_victim, v.surname, v.first_names, v.birth_year, 
				bc.country AS birth_country, v.birth_place, n.nationality AS nationality_1938, et.ethnic_group
		FROM nmv__victim v
		LEFT JOIN nmv__country bc 								ON bc.ID_country = v.ID_birth_country
		LEFT JOIN nmv__victim_experiment ve						ON v.ID_victim = ve.ID_victim
		LEFT JOIN nmv__survival s 								ON s.ID_survival = ve.ID_survival
		LEFT JOIN nmv__experiment ex 							ON ve.ID_experiment = ex.ID_experiment
		LEFT JOIN nmv__experiment_foi ef						ON ef.ID_experiment = ex.ID_experiment
		LEFT JOIN nmv__field_of_interest foi					ON foi.ID_foi = ef.ID_foi
		LEFT JOIN nmv__imprisonment i							ON v.ID_victim = i.ID_victim
		LEFT JOIN nmv__imprisonment_classification ic 			ON ic.ID_imprisonment = i.ID_imprisonment
		LEFT JOIN nmv__nationality n        					ON n.ID_nationality = v.ID_nationality_1938
		LEFT JOIN nmv__ethnic_group et       					ON et.ID_ethnic_group = v.ID_ethnic_group
		LEFT JOIN nmv__med_history_brain b						ON v.ID_victim = b.ID_victim
		LEFT JOIN nmv__diagnosis_brain db 						ON db.ID_med_history_brain = b.ID_med_history_brain
		LEFT JOIN nmv__diagnosis_tag dtb						ON dtb.ID_diagnosis = db.ID_diagnosis
		LEFT JOIN nmv__med_history_tissue t						ON v.ID_victim = t.ID_victim
		LEFT JOIN nmv__med_history_hosp h						ON v.ID_victim = h.ID_victim
		LEFT JOIN nmv__diagnosis_hosp dh						ON dh.ID_med_history_hosp = h.ID_med_history_hosp
		LEFT JOIN nmv__diagnosis_tag dth						ON dth.ID_diagnosis = dh.ID_diagnosis
		LEFT JOIN nmv__med_history_diagnosis d					ON d.ID_victim = v.ID_victim
		LEFT JOIN nmv__diagnosis_diagnosis dd 					ON dd.ID_med_history_diagnosis = d.ID_med_history_diagnosis
		LEFT JOIN nmv__diagnosis_tag dtd 						ON dtd.ID_diagnosis = dd.ID_diagnosis
		LEFT JOIN nmv__victim_source vs 						ON vs.ID_victim = v.ID_victim
		LEFT JOIN nmv__victim_literature vl 					ON vl.ID_victim = v.ID_victim
		LEFT JOIN nmv__institution di							ON di.ID_institution = v.ID_death_institution
	'; // für Ergebnisliste}
endif;
$querystring_where = array(); // for where-part of select clause
$querystring_where[] = "was_prisoner_assistant != 'prisoner assistant only'";

// include only profiles where there does not exist a new one from mpg-project
//$querystring_where[] = "v.ID_new_profile IS NULL";

//complete db d
if ($dbi->checkUserPermission('mpg')) :
	if (getUrlParameter('potential_old_profile') == -1) :
		$querystring_where[] = '(v.mpg_project = -1 OR v.potential_old_profile = -1)';
	else :
		$querystring_where[] = '(v.mpg_project = -1)';
	endif;
endif;



// define MySQL-characterfilter (Trunkierungszeichen werden zu MySQL-Zeichen)
$filter_chars = array('\'', '%', '_', '*', '٭');
$replace_chars = array('_', ' ', ' ', '%', '%');

// WHERE Strings zusammenbauen

foreach ($exact_fields as $field) {
    if (getUrlParameter($field)) {
			if ($field == 'photo') {
				$querystring_where[] = "(vs.source_has_photo = -1 OR
																 vl.literature_has_photo = -1 OR
																 b.brain_report_has_photo = -1 OR
																 h.hosp_has_photo = -1 OR
																 v.photo_exists = -1)";
			}
			elseif ($field == 'clinical_ID_diagnosis') {
				$querystring_where[] = "(dd.ID_diagnosis = '".getUrlParameter($field)."' OR
											dh.ID_diagnosis = '".getUrlParameter($field)."')";
			}
			else {
				if (getUrlParameter($field) == 'NULL') {
					$querystring_where[] = "v.$field IS NULL";
				} else {
        	$querystring_where[] = "v.$field = '".getUrlParameter($field)."'";
				}
			}
    }
}
foreach ($like_fields as $field) {
    if (getUrlParameter($field)) {
			$filtered_field = str_replace($filter_chars, $replace_chars, getUrlParameter($field));
			if (getUrlParameter($field) == 'NULL') {
				$querystring_where[] = "v.$field IS NULL";
			} else {
				$querystring_where[] = "v.$field LIKE TRIM('".$filtered_field."')";
			}
    }
}
foreach ($contain_fields as $field) {
    if (getUrlParameter($field)) {
			$filtered_field = str_replace($filter_chars, $replace_chars, getUrlParameter($field));
			if (getUrlParameter($field) == 'NULL') {
				$querystring_where[] = "v.$field IS NULL";
			} else {
				$querystring_where[] = "v.$field LIKE '%".$filtered_field."%'";
			}
    }
}
foreach ($double_fields as $field) {
    if (getUrlParameter($field)) {
			$filtered_field = str_replace($filter_chars, $replace_chars, getUrlParameter($field));
			if (getUrlParameter($field) == 'NULL') {
				$querystring_where[] = "v.$field IS NULL";
			} else {
				$querystring_where[] = "(v.$field LIKE '".$filtered_field."' OR v.$field = '".getUrlParameter($field)."')";
			}
		}
}
foreach ($special_fields as $key=>$field) {
    if (getUrlParameter($field)) {
			if (getUrlParameter($field) == 'NULL') {
				$querystring_where[] = "$key IS NULL";
			} else {
				$querystring_where[] = "$key = '".getUrlParameter($field)."'";
			}
    }
}
foreach ($special_contain_fields as $key=>$field) {
    if (getUrlParameter($field)) {
			$filtered_field = str_replace($filter_chars, $replace_chars, getUrlParameter($field));
			if (getUrlParameter($field) == 'NULL') {
				$querystring_where[] = "$key IS NULL";
			} else {
				$querystring_where[] = "$key LIKE '%".$filtered_field."%'";
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
$querystring_items .= " GROUP BY v.ID_victim, ve.ID_vict_exp";
//echo $querystring_items;
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
	$search_term = $dbi->connection->query('SELECT country FROM nmv__country WHERE ID_country = '.$_GET['ID_birth_country'])->fetch_row();
	if(empty($search_term)){
		$suche_nach[] = 'country of birth has no entry';
	}	else {
		$suche_nach[] = 'country of birth = '.$search_term[0];
	}
}
if (isset($_GET['birth_place']) && $_GET['birth_place']) $suche_nach[] = 'place of birth contains:  '.$_GET['birth_place'];
if (isset($_GET['birth_year']) && $_GET['birth_year']) $suche_nach[] = 'year of birth = '.$_GET['birth_year'];
if (isset($_GET['twin']) && $_GET['twin']) $suche_nach[] = 'twins only';
if (isset($_GET['ID_death_institution']) && $_GET['ID_death_institution'])  {
	$search_term = $dbi->connection->query('SELECT institution_name FROM nmv__institution WHERE ID_institution = '.$_GET['ID_death_institution'])->fetch_row();
	if(empty($search_term)){
		$suche_nach[] = 'institution of death = NULL';
	} else {
		$suche_nach[] = 'institution of death = '.$search_term[0];
	}
}
if (isset($_GET['ID_death_country']) && $_GET['ID_death_country'])  {
	$search_term = $dbi->connection->query('SELECT country FROM nmv__country WHERE ID_country = '.$_GET['ID_death_country'])->fetch_row();
	if(empty($search_term)){
		$suche_nach[] = 'country of death = NULL';
	}	else {
		$suche_nach[] = 'country of death = '.$search_term[0];
	}
}
if (isset($_GET['death_place']) && $_GET['death_place']) $suche_nach[] = 'place of death contains:  '.$_GET['death_place'];
if (isset($_GET['death_year']) && $_GET['death_year']) $suche_nach[] = 'year of death = '.$_GET['death_year'];
if (isset($_GET['gender']) && $_GET['gender']) $suche_nach[] = 'gender = '.$_GET['gender'];
if (isset($_GET['ID_religion']) && $_GET['ID_religion']) {
	$search_term = $dbi->connection->query('SELECT religion FROM nmv__religion WHERE ID_religion = '.$_GET['ID_religion'])->fetch_row();
	if(empty($search_term)){
		$suche_nach[] = 'religion = NULL';
	} else {
		$suche_nach[] = 'religion = '.$search_term[0];
	}
}
if (isset($_GET['ID_ethnic_group']) && $_GET['ID_ethnic_group']) {
	$search_term = $dbi->connection->query('SELECT ethnic_group FROM nmv__ethnic_group WHERE ID_ethnic_group = '.$_GET['ID_ethnic_group'])->fetch_row();
	if(empty($search_term)){
 		$suche_nach[] = 'ethnic group = NULL';
 	} else {
		$suche_nach[] = 'ethnic group = '.$search_term[0];
	}
}
if (isset($_GET['ID_nationality_1938']) && $_GET['ID_nationality_1938']) {
	$search_term = $dbi->connection->query('SELECT nationality  FROM nmv__nationality WHERE ID_nationality = '.$_GET['ID_nationality_1938'])->fetch_row();
	if(empty($search_term)){
		$suche_nach[] = 'nationality in 1938 = NULL';
	}	else {
		$suche_nach[] = 'nationality in 1938 = '.$search_term[0];
	}
}
if (isset($_GET['ID_education']) && $_GET['ID_education']) {
	$search_term = $dbi->connection->query('SELECT education FROM nmv__education WHERE ID_education = '.$_GET['ID_education'])->fetch_row();
	if(empty($search_term)){
		$suche_nach[] = 'education = NULL';
	}	else {
		$suche_nach[] = 'education = '.$search_term[0];
	}
}
if (isset($_GET['ID_occupation']) && $_GET['ID_occupation']) {
	$search_term = $dbi->connection->query('SELECT occupation FROM nmv__occupation WHERE ID_occupation = '.$_GET['ID_occupation'])->fetch_row();
	if(empty($search_term)){
		$suche_nach[] = 'occupation = NULL';
	}	else {
		$suche_nach[] = 'occupation = '.$search_term[0];
	}
}
if (isset($_GET['ID_arrest_country']) && $_GET['ID_arrest_country']) {
	$search_term = $dbi->connection->query('SELECT country FROM nmv__country WHERE ID_country = '.$_GET['ID_arrest_country'])->fetch_row();
	if(empty($search_term)){
		$suche_nach[] = 'country of arrest = NULL';
	}	else {
		$suche_nach[] = 'country of arrest = '.$search_term[0];
	}
}
if (isset($_GET['arrest_history']) && $_GET['arrest_history']) $suche_nach[] = 'arrest history contains:  '.$_GET['arrest_history'];
if (isset($_GET['ID_experiment']) && $_GET['ID_experiment']) {
	$search_term = $dbi->connection->query('SELECT experiment_title FROM nmv__experiment WHERE ID_experiment = '.$_GET['ID_experiment'])->fetch_row();
	if(empty($search_term)){
		$suche_nach[] = 'no experiment linked';
	}	else {
		$suche_nach[] = 'title of experiment = '.$search_term[0];
	}
}
if (isset($_GET['ID_foi']) && $_GET['ID_foi']) {
	$search_term = $dbi->connection->query('SELECT field_of_interest FROM nmv__field_of_interest WHERE ID_foi = '.$_GET['ID_foi'])->fetch_row();
	$suche_nach[] = 'field of interest = '.$search_term[0];
}
if (isset($_GET['exp_institution']) && $_GET['exp_institution']) {
	$search_term = $dbi->connection->query('SELECT institution_name FROM nmv__institution WHERE ID_institution = '.$_GET['exp_institution'])->fetch_row();
	$suche_nach[] = 'institution of experiment = '.$search_term[0];
}
if (isset($_GET['ID_classification']) && $_GET['ID_classification']) {
	$search_term = $dbi->connection->query('SELECT classification FROM nmv__victim_classification WHERE ID_classification = '.$_GET['ID_classification'])->fetch_row();
	$suche_nach[] = 'imprisonment classification = '.$search_term[0];
}
if (isset($_GET['location']) && $_GET['location']) $suche_nach[] = 'imprisonment location = '.$_GET['location'];
if (isset($_GET['ID_imprisonment_institution']) && $_GET['ID_imprisonment_institution']) {
	$search_term = $dbi->connection->query('SELECT institution_name FROM nmv__institution WHERE ID_institution = '.$_GET['ID_imprisonment_institution'])->fetch_row();
	$suche_nach[] = 'institution of imprisonment = '.$search_term[0];
}
if (isset($_GET['ID_evaluation_status']) && $_GET['ID_evaluation_status']) {
	$search_term = $dbi->connection->query('SELECT status FROM nmv__victim_evaluation_status WHERE ID_evaluation_status = '.$_GET['ID_evaluation_status'])->fetch_row();
	if(empty($search_term)){
		$suche_nach[] = 'ID_evaluation status = NULL';
	}	else {
		$suche_nach[] = 'ID_evaluation status = '.$search_term[0];
	}
}
if (isset($_GET['mpg_project']) && $_GET['mpg_project']) $suche_nach[] = 'mpg_project only';
if (isset($_GET['ID_dataset_origin']) && $_GET['ID_dataset_origin']) {
	$search_term = $dbi->connection->query('SELECT work_group FROM nmv__dataset_origin WHERE ID_dataset_origin = '.$_GET['ID_dataset_origin'])->fetch_row();
	$suche_nach[] = 'workgroup(s) = '.$search_term[0];
}if (isset($_GET['entry_status']) && $_GET['entry_status']) {
	$suche_nach[] = 'Status data transfer = '.$_GET['entry_status'];
}
if (isset($_GET['tissue_institution']) && $_GET['tissue_institution']) {
	$search_term = $dbi->connection->query('SELECT institution_name FROM nmv__institution WHERE ID_institution = '.$_GET['tissue_institution'])->fetch_row();
	$suche_nach[] = 'location of tissue = '.$search_term[0];
}
if (isset($_GET['ID_tissue_form']) && $_GET['ID_tissue_form']) {
	$search_term = $dbi->connection->query('SELECT tissue_form FROM nmv__tissue_form WHERE ID_tissue_form = '.$_GET['ID_tissue_form'])->fetch_row();
	$suche_nach[] = 'form of tissue = '.$search_term[0];
}
if (isset($_GET['ID_tissue_state']) && $_GET['ID_tissue_state']) {
	$search_term = $dbi->connection->query('SELECT tissue_state FROM nmv__tissue_state WHERE ID_tissue_state = '.$_GET['ID_tissue_state'])->fetch_row();
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
if (isset($_GET['clinical_ID_diagnosis']) && $_GET['clinical_ID_diagnosis']) {
	$search_term = $dbi->connection->query('SELECT diagnosis FROM nmv__diagnosis_tag WHERE ID_diagnosis = '.$_GET['clinical_ID_diagnosis'])->fetch_row();
	$suche_nach[] = 'clinical diagnosis tag  = '.$search_term[0];
}
if (isset($_GET['ref_no_brain']) && $_GET['ref_no_brain']) $suche_nach[] = 'RefNo Brain report contains:  '.$_GET['ref_no_brain'];

if (isset($_GET['hospitalisation_year']) && $_GET['hospitalisation_year']) $suche_nach[] = 'year of hospitalisation = '.$_GET['hospitalisation_year'];
if (isset($_GET['hospitalisation_institution']) && $_GET['hospitalisation_institution']) {
	$search_term = $dbi->connection->query('SELECT institution_name FROM nmv__institution WHERE ID_institution = '.$_GET['hospitalisation_institution'])->fetch_row();
	$suche_nach[] = 'institution of hospitalisation = '.$search_term[0];
}
if (isset($_GET['clinical_diagnosis']) && $_GET['clinical_diagnosis']) $suche_nach[] = 'clinical diagnosis contains: '.$_GET['clinical_diagnosis'];
if (isset($_GET['autopsy_ref_no']) && $_GET['autopsy_ref_no']) $suche_nach[] = 'AutopsyRefNo Hospitalisation contains:  '.$_GET['autopsy_ref_no'];

if (isset($_GET['residence_after_1945_country']) && $_GET['residence_after_1945_country']) $suche_nach[] = 'residence after 1945 (country) = '.$_GET['residence_after_1945_country'];
if (isset($_GET['occupation_after_1945']) && $_GET['occupation_after_1945']) $suche_nach[] = 'occupation after 1945 = '.$_GET['occupation_after_1945'];
if (isset($_GET['ID_nationality_after_1945']) && $_GET['ID_nationality_after_1945']) {
	$search_term = $dbi->connection->query('SELECT nationality FROM nmv__nationality WHERE ID_nationality = '.$_GET['ID_nationality_after_1945'])->fetch_row();
	$suche_nach[] = 'nationality after 1945 = '.$search_term[0];
}
if (isset($_GET['notes']) && $_GET['notes']) $suche_nach[] = 'notes = ... '.$_GET['notes'] . ' ...';
if (isset($_GET['notes_after_1945']) && $_GET['notes_after_1945']) $suche_nach[] = 'notes after 1945 = ... ' . $_GET['notes_after_1945'] . ' ...';
if (isset($_GET['photo']) && $_GET['photo']) $suche_nach[] = 'photo contained';
if (isset($_GET['stolperstein_exists']) && $_GET['stolperstein_exists']) $suche_nach[] = 'known stolperstein';


// breadcrumbs
$dbi->addBreadcrumb (L_SEARCH,'search.php');

// layout
if((isset($_GET['ID_experiment']) && $_GET['ID_experiment']) || (isset($_GET['exp_institution']) && $_GET['exp_institution'])) { // special table for experiment-related filters: shows and links victim-experiment data

	$layout
		->set('title',L_RESULTS)
		->set('content',
	        '<p>Search for: <em>'.implode(' AND ', $suche_nach).'</em><br>
					Number of results: '. $total_results->total. '</p>'
					. '<div class="buttons">'.createButton ('Export Table to .csv',"nmv_export.php?type=csv&entity=victim&where-clause=$where_clause_encoded",'icon download')
																	 .createButton ('Export Table to .xls',"nmv_export.php?type=xls&entity=victim&where-clause=$where_clause_encoded",'icon download')
					. '</div>'
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
					. '<div class="buttons">'.createButton ('Export Table to .csv',"nmv_export.php?type=csv&entity=victim&where-clause=$where_clause_encoded",'icon download')
																	 .createButton ('Export Table to .xls',"nmv_export.php?type=xls&entity=victim&where-clause=$where_clause_encoded",'icon download')
					. '</div>'
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
