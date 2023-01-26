<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');

// get ID_victim / _experiment
$victim_id = (int) getUrlParameter('ID_victim', 0);
$experiment_id = (int) getUrlParameter('ID_experiment', 0);

$victim_name = 'Error: Missing victim.';
$experiment_name = 'Error: Missing biomedical research.';
$title = '';
$content = '';

//create a table of prisoner assistants of a certain experiment

$dbi->addBreadcrumb ('Biomedical research','nmv_list_experiments');

// query: get experiment data
$querystring = "
SELECT CONCAT(COALESCE(experiment_title, '')) experiment_name
FROM nmv__experiment
WHERE ID_experiment = $experiment_id";
$query = $dbi->connection->query($querystring);
$experiment = $query->fetch_object();

$experiment_name = $experiment->experiment_name;

//browsing options --> $_GET in url
$dbi->setUserVar('querystring', "ID_experiment=$experiment_id");
$dbi->setUserVar('sort',getUrlParameter('sort'),'surname');
$dbi->setUserVar('order',getUrlParameter('order'),'ASC');
$dbi->setUserVar('skip',getUrlParameter('skip'),0);


$dbi->addBreadcrumb ($experiment_name,'nmv_view_experiment?ID_experiment='.$experiment_id);

// reconstruct GET-String (for scroll- and order- / sort- function)


// query: get data of the victims of the experiment

$querystring_items = "SELECT
                          pa.ID_victim, pae.ID_pa_exp, pa.surname AS surname, pa.first_names,
                          pa.birth_year, bc.english AS birth_country,
                          n.english AS nationality_1938,
                          et.english AS ethnic_group, pae.exp_start_day, pae.exp_start_month, pae.exp_start_year
                      FROM nmv__prisoner_assistant_experiment pae
                      LEFT JOIN nmv__victim pa                    ON pa.ID_victim = pae.ID_victim
                    	LEFT JOIN nmv__country bc                  ON bc.ID_country = pa.ID_birth_country
                    	LEFT JOIN nmv__nationality n               ON n.ID_nationality = pa.nationality_1938
                    	LEFT JOIN nmv__ethnic_group et              ON et.ID_ethnic_group = pa.ethnic_group
                      WHERE pae.ID_experiment = $experiment_id";

// Gesamtzahl der Suchergebnisse feststellen
$querystring_count = "SELECT COUNT(*) AS total FROM ($querystring_items) AS xyz";
$query_count = $dbi->connection->query($querystring_count);
$total_results = $query_count->fetch_object();
//$victim_count = $total_results->total;
$dbi->setUserVar('total_results',$total_results->total);

// order-klausel
// version with Z_LIST_ROWS_PAGE victims per page and pagination:
//$querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']}".($dbi->user['skip']).','.Z_LIST_ROWS_PAGE;
//version with all victims on one page:
$querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']}";

// query ausführen
$query_items = $dbi->connection->query($querystring_items.$querystring_orderby);

//layout
$layout
	->set('title','Prisoner Assistants of Experiment')
	->set('content',
      "<br><p>Title of Experiment:<strong> $experiment_name </strong><br>
      Number of Prisoner Assistants:<strong> $total_results->total</strong></p>"
      . '<br>'
	    . $dbi->getListView('table_nmv_pa_exp',$query_items)
      . createBackLink ('View Biomedical Research: '.$experiment_name,'nmv_view_experiment?ID_experiment='.$experiment_id)
	)
	->cast();
