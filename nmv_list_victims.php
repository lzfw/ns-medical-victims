<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

// browsing options
$dbi->setUserVar ('sort',getUrlParameter('sort'),'ID_victim');
$dbi->setUserVar ('order',getUrlParameter('order'),'ASC');
$dbi->setUserVar ('skip',getUrlParameter('skip'),0);

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');


// Select-Klauseln erstellen
$querystring_count = "SELECT COUNT(v.ID_victim) AS total FROM nmv__victim v WHERE v.was_prisoner_assistant != 'prisoner assistant only'"; // für Treffer gesamt
$querystring_items = "SELECT v.ID_victim, v.surname, v.first_names, v.birth_place, v.birth_year, v.birth_month, v.birth_day,
											m.marital_family_status, e.education AS education, bc.country AS birth_country,
											v.death_year, v.death_month, v.death_day, v.death_place, dc.country AS death_country, v.cause_of_death,
											v.gender, r.religion, n.nationality AS nationality_1938, et.ethnic_group,
											o.occupation, v.occupation_details, v.twin, v.arrest_location,
											ac.country AS arrest_country, v.residence_after_1945_place, v.residence_after_1945_country, v.occupation_after_1945,
											na.nationality AS nationality_after_1945, v.mpg_project, da.work_group  AS dataset_origin, es.status as evaluation_status
	FROM nmv__victim v
	LEFT JOIN nmv__marital_family_status m ON m.ID_marital_family_status = v.ID_marital_family_status
	LEFT JOIN nmv__education e ON e.ID_education = v.ID_education
	LEFT JOIN nmv__country bc ON bc.ID_country = v.ID_birth_country
	LEFT JOIN nmv__country dc ON dc.ID_country = v.ID_death_country
	LEFT JOIN nmv__country ac ON ac.ID_country = v.ID_arrest_country
	LEFT JOIN nmv__religion r ON r.ID_religion = v.ID_religion
	LEFT JOIN nmv__nationality n ON n.ID_nationality = v.ID_nationality_1938
	LEFT JOIN nmv__nationality na ON na.ID_nationality = v.ID_nationality_after_1945
	LEFT JOIN nmv__ethnic_group et ON et.ID_ethnic_group = v.ID_ethnic_group
	LEFT JOIN nmv__occupation o ON o.ID_occupation = v.ID_occupation
	LEFT JOIN nmv__dataset_origin da ON da.ID_dataset_origin = v.ID_dataset_origin
  LEFT JOIN nmv__victim_evaluation_status es ON es.ID_evaluation_status = v.ID_evaluation_status
	WHERE v.was_prisoner_assistant != 'prisoner assistant only'"; // für Ergebnisliste

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
	->set('title','Victims')
	->set('content',
			($dbi->checkUserPermission('edit')
	        ? '<div class="buttons">'.createButton ('New Victim','nmv_edit_victim?type=victim','icon addUser').'</div>'
	        : '')
			// . '<div class="buttons">'.createButton ('Export Table to .csv','nmv_export.php?type=csv&entity=victim&where-clause=','icon download')
			// 															.createButton ('Export Table to .xls','nmv_export.php?type=xls&entity=victim&where-clause=','icon download')
			// . '</div>'
	    . $dbi->getListView('nmv_victims_table',$query_items)
	    . ($dbi->checkUserPermission('edit')
	        ? '<div class="buttons">'.createButton ('New Victim','nmv_edit_victim','icon addUser').'</div>'
	        : '')
	    . createBackLink (L_CONTENTS,'z_menu_contents')
	)
	->cast();
