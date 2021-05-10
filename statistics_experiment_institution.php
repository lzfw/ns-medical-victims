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

// url parameters
$dbi->setUserVar ('view',getUrlParameter('view'),'default');
// browsing options
$dbi->setUserVar ('sort',getUrlParameter('sort'),'institution');
$dbi->setUserVar ('order',getUrlParameter('order'),'ASC');

//Query gender / nationality
$querystring_items = 'SELECT A.institution as institution, A.ID_institution as ID_institution, A.number as anumber, B.number as bnumber
                      FROM 	(SELECT i.institution_name as institution, i.ID_institution as ID_institution, COUNT(v.ID_victim) as number
                      		  FROM nmv__victim v
                      		  INNER JOIN nmv__victim_experiment ve ON v.ID_victim = ve.ID_victim
                            INNER JOIN nmv__experiment e ON ve.ID_experiment = e.ID_experiment
                            LEFT JOIN nmv__institution i ON e.ID_institution = i.ID_institution
                            WHERE v.nationality_1938 = 383
                            GROUP BY i.ID_institution) A

                           LEFT JOIN

                           	(SELECT i.institution_name as institution, i.ID_institution as ID_institution, COUNT(v.ID_victim) as number
                      		  FROM nmv__victim v
                      		  INNER JOIN nmv__victim_experiment ve ON v.ID_victim = ve.ID_victim
                            INNER JOIN nmv__experiment e ON ve.ID_experiment = e.ID_experiment
                            LEFT JOIN nmv__institution i ON e.ID_institution = i.ID_institution
                            WHERE v.mpg_project = -1 AND v.nationality_1938 = 383
                            GROUP BY i.ID_institution) B

                           ON A.ID_institution = B.ID_institution OR (A.ID_institution IS NULL AND B.ID_institution IS NULL)';
$querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']}";

// execute query
$query_items = $dbi->connection->query($querystring_items.$querystring_orderby);

$layout
	->set('title','Statistic - Experiment (Institution)');
$layout
	->set('content',
          '<p><br>
						Numbers of persons with <strong>Czechoslovakian nationality (1938)</strong> who where victim of experiments in different locations.
						<br> <strong>One person can be victim of different experiments that took place in different locations.
						<br> Not all victims are assigned to an experiment.</strong>
						<br> The total number in this table therefore does not correspond to the number of victims in the database, but to the number of victims linked to an experiment.
					</p>'
					// Tabelle bauen
					.$dbi->getListView('statistics_experiment_institution',$query_items)
          .'<br><br>'
          .createButton('Back','javascript:history.back()')
          .createButton('Forward', 'javascript:history.forward()')
        );
require_once 'statistics_navigation.php'; // navigation to different statistics
$layout->cast();

?>
