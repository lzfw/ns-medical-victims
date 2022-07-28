<?php
/**
*creates statistics for the experiment institution
*
*
*
*/


require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('view');
//complete db
$dbi->denyUserPermission ('mpg');

$nationality_select = new Form ('experiment_institution','statistics_experiment_institution','GET');
$nationality_select->addConnection(MYSQL_DB,$db_host,$db_user,$db_pass,$db_name);
$nationality_select->addField ('ID_nationality',SELECT)
  ->setLabel('Nationality (1938)')
  ->addOption(NO_VALUE, 'all victims')
  ->addOption(999, 'no nationality given')
  ->addOptionsFromQuery(" SELECT n.ID_nationality AS value, n.english AS title
                          FROM nmv__nationality n
                          WHERE EXISTS (SELECT v.ID_victim
                                        FROM nmv__victim v
                                        WHERE v.nationality_1938 = n.ID_nationality)
                          ORDER BY english");

$nationality_select
->addButton(SUBMIT,'OK');

//get experiment-data
$nationality_id = (int) getUrlParameter('ID_nationality', 0);
$nationality_query = "SELECT english
          FROM nmv__nationality
          WHERE ID_nationality=$nationality_id";
$query_item = $dbi->connection->query($nationality_query)->fetch_array();
$nationality_english = $query_item['english'];

if(is_int($nationality_id) && $nationality_id != 999):
  $where_clause = "v.nationality_1938 = $nationality_id";
elseif($nationality_id == 999):
  $where_clause = "v.nationality_1938 IS NULL";
else:
  $where_clause = "1";
endif;


// url parameters
$dbi->setUserVar ('view',getUrlParameter('view'),'default');
// browsing options
$dbi->setUserVar ('sort',getUrlParameter('sort'),'institution');
$dbi->setUserVar ('order',getUrlParameter('order'),'ASC');



//Query gender / nationality
if($nationality_id):
  $querystring_items = "SELECT A.institution as institution, A.ID_institution as ID_institution, A.number as anumber, B.number as bnumber
                      FROM 	(SELECT i.institution_name as institution, i.ID_institution as ID_institution, COUNT(v.ID_victim) as number
                      		  FROM nmv__victim v
                      		  INNER JOIN nmv__victim_experiment ve ON v.ID_victim = ve.ID_victim
                            INNER JOIN nmv__experiment_institution ei ON ve.ID_experiment = ei.ID_experiment
                            LEFT JOIN nmv__institution i ON ei.ID_institution = i.ID_institution
                            WHERE $where_clause
                            GROUP BY i.ID_institution) A

                           LEFT JOIN

                           	(SELECT i.institution_name as institution, i.ID_institution as ID_institution, COUNT(v.ID_victim) as number
                      		  FROM nmv__victim v
                      		  INNER JOIN nmv__victim_experiment ve ON v.ID_victim = ve.ID_victim
                            INNER JOIN nmv__experiment_institution ei ON ve.ID_experiment = ei.ID_experiment
                            LEFT JOIN nmv__institution i ON ei.ID_institution = i.ID_institution
                            WHERE $where_clause AND v.mpg_project = -1
                            GROUP BY i.ID_institution) B

                           ON A.ID_institution = B.ID_institution OR (A.ID_institution IS NULL AND B.ID_institution IS NULL)" ;
else:
  $querystring_items = "SELECT A.institution as institution, A.ID_institution as ID_institution, A.number as anumber, B.number as bnumber
                        FROM 	(SELECT i.institution_name as institution, i.ID_institution as ID_institution, COUNT(v.ID_victim) as number
                        		  FROM nmv__victim v
                        		  INNER JOIN nmv__victim_experiment ve ON v.ID_victim = ve.ID_victim
                              INNER JOIN nmv__experiment_institution ei ON ve.ID_experiment = ei.ID_experiment
                              LEFT JOIN nmv__institution i ON ei.ID_institution = i.ID_institution
                              GROUP BY i.ID_institution) A

                             LEFT JOIN

                             	(SELECT i.institution_name as institution, i.ID_institution as ID_institution, COUNT(v.ID_victim) as number
                        		  FROM nmv__victim v
                        		  INNER JOIN nmv__victim_experiment ve ON v.ID_victim = ve.ID_victim
                              INNER JOIN nmv__experiment_institution ei ON ve.ID_experiment = ei.ID_experiment
                              LEFT JOIN nmv__institution i ON ei.ID_institution = i.ID_institution
                              WHERE v.mpg_project = -1
                              GROUP BY i.ID_institution) B

                             ON A.ID_institution = B.ID_institution OR (A.ID_institution IS NULL AND B.ID_institution IS NULL)" ;
endif;
$querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']}";

// execute query
$query_items = $dbi->connection->query($querystring_items.$querystring_orderby);

$layout
	->set('title','Statistic - Experiment (Institution)');
$layout
	->set('content',
          "<br><div>Mouseclick on dropdown-bar and choose nationality (1938)<br>
                    Then click OK-button <br> <br>"
            . $nationality_select->run() .

          "<p><br>
						Numbers of persons who where victim of experiments in different locations.
						<br> <strong>One person can be victim of different experiments that took place in different locations.
						<br> Not all victims are assigned to an experiment.</strong>
						<br> The total number in this table therefore does not correspond to the number of victims in the database, but to the number of victims linked to an experiment.
					</p>
          <br><hr><hr><br>
          <h2> " . $nationality_english . " Victims </h2>"
					// Tabelle bauen
					.$dbi->getListView('statistics_experiment_institution',$query_items)
          ."<br><br>"
          .createButton('Back','javascript:history.back()')
          .createButton('Forward', 'javascript:history.forward()')
        );
require_once 'statistics_navigation.php'; // navigation to different statistics
$layout->cast();

?>
