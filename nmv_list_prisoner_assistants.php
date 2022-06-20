<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

// browsing options
$dbi->setUserVar ('sort',getUrlParameter('sort'),'ID_victim');
$dbi->setUserVar ('order',getUrlParameter('order'),'ASC');
$dbi->setUserVar ('skip',getUrlParameter('skip'),0);

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');

//TODO -> überflüsssig?
// $options = '';
// if ($dbi->checkUserPermission('edit')) {
// 		$options .= createSmallButton(L_EDIT,'nmv_edit_victim?type=prisoner_assistant&ID_victim={ID_victim}','icon edit');
// }
// if ($dbi->checkUserPermission('admin')) {
// 		$options .= createSmallButton(L_DELETE,'nmv_remove_victim?ID_victim={ID_victim}','icon delete');
// }
// $options .= createSmallButton("medical history",'nmv_list_med_hist?ID_victim={ID_victim}','icon report-paper');

// Select-Klauseln erstellen
$querystring_count = "SELECT COUNT(v.ID_victim) AS total FROM nmv__victim v WHERE v.was_prisoner_assistant != 'victim only'"; // für Treffer gesamt
$querystring_items = "	SELECT v.ID_victim, v.surname, v.first_names, v.birth_place, v.birth_year, v.birth_month, v.birth_day,
	m.english AS marital_family_status, e.english AS education, bc.english AS birth_country,
	v.death_year, v.death_month, v.death_day, v.death_place, dc.english AS death_country, v.cause_of_death,
	v.gender, r.english AS religion, n.english AS nationality_1938, et.english AS ethnic_group,
	o.english AS occupation, v.occupation_details, v.twin, v.arrest_location,
	ac.english AS arrest_country, v.residence_after_1945_place, v.residence_after_1945_country,
	v.occupation_after_1945, na.english AS nationality_after_1945, v.mpg_project,
	da.work_group  AS dataset_origin, es.english as evaluation_status
	FROM nmv__victim v
	LEFT JOIN nmv__marital_family_status m ON m.ID_marital_family_status = v.ID_marital_family_status
	LEFT JOIN nmv__education e ON e.ID_education = v.ID_education
	LEFT JOIN nmv__country bc ON bc.ID_country = v.ID_birth_country
	LEFT JOIN nmv__country dc ON dc.ID_country = v.ID_death_country
	LEFT JOIN nmv__country ac ON ac.ID_country = v.ID_arrest_country
	LEFT JOIN nmv__religion r ON r.ID_religion = v.religion
	LEFT JOIN nmv__nationality n ON n.ID_nationality = v.nationality_1938
	LEFT JOIN nmv__nationality na ON na.ID_nationality = v.nationality_after_1945
	LEFT JOIN nmv__ethnicgroup et ON et.ID_ethnicgroup = v.ethnic_group
	LEFT JOIN nmv__occupation o ON o.ID_occupation = v.occupation
	LEFT JOIN nmv__dataset_origin da ON da.ID_dataset_origin = v.ID_dataset_origin
	LEFT JOIN nmv__evaluation ev ON ev.ID_victim = v.ID_victim
  LEFT JOIN nmv__victim_evaluation_status es ON es.ID_status = ev.evaluation_status
  WHERE v.was_prisoner_assistant != 'victim only'"; // für Ergebnisliste

//complete db d
if ($dbi->checkUserPermission('mpg')) :
	$querystring_count .= ' AND mpg_project = -1';
	$querystring_items .= ' AND mpg_project = -1';
endif;


// Gesamtanzahl der Suchergebnisse feststellen
$query_count = $dbi->connection->query($querystring_count);
$total_results = $query_count->fetch_object();
$dbi->setUserVar('total_results',$total_results->total);

// order-klausel
// version with Z_LIST_ROWS_PAGE victims per page and pagination:
$querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']} LIMIT ".($dbi->user['skip']).','.Z_LIST_ROWS_PAGE;
//version with all victims on one page:
//$querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']}";


// query ausführen
$query_items = $dbi->connection->query($querystring_items.$querystring_orderby);

$layout
	->set('title','Prisoner Assistants')
	->set('content',
			($dbi->checkUserPermission('edit')
	        ? '<div class="buttons">'.createButton ('New Prisoner Assistant','nmv_edit_victim?type=prisoner_assistant','icon addUser').'</div>'
	        : '') .
	    $dbi->getListView('nmv_victims_table',$query_items)
	    .($dbi->checkUserPermission('edit')
	        ? '<div class="buttons">'.createButton ('New Prisoner Assistant','nmv_edit_victim?type=prisoner_assistant','icon addUser').'</div>'
	        : '')
	    .createBackLink (L_CONTENTS,'z_menu_contents')
	)
	->cast();
