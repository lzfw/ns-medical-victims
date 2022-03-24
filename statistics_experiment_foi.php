<?php
/**
*creates statistics for field of interest of experiment
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
$dbi->setUserVar ('sort',getUrlParameter('sort'),'field_of_interest');
$dbi->setUserVar ('order',getUrlParameter('order'),'ASC');


//Query field of interest
		//query
		$querystring_items = '	SELECT A.english as field_of_interest, A.experiments AS experiments, A.number as anumber, B.number as bnumber
														FROM 	(SELECT COUNT(v.ID_victim) AS number, GROUP_CONCAT(DISTINCT "ID ", e.ID_experiment, ": ", e.experiment_title SEPARATOR "<br>") AS experiments, foi.english
																		FROM nmv__experiment e
																		INNER JOIN nmv__victim_experiment ve ON ve.ID_experiment = e.ID_experiment
																		LEFT JOIN nmv__victim v ON v.ID_victim = ve.ID_victim
																		LEFT JOIN nmv__experiment_foi ef ON ef.ID_experiment = e.ID_experiment
																		LEFT JOIN nmv__field_of_interest foi ON foi.ID_foi = ef.ID_foi
																		GROUP BY ef.ID_foi) A
														      LEFT JOIN
																	(SELECT COUNT(v.ID_victim) AS number, GROUP_CONCAT(DISTINCT "ID ", e.ID_experiment, ": ", e.experiment_title SEPARATOR "<br>") AS experiments, foi.english
																		FROM nmv__experiment e
																		INNER JOIN nmv__victim_experiment ve ON ve.ID_experiment = e.ID_experiment
																		LEFT JOIN nmv__victim v ON v.ID_victim = ve.ID_victim
																		LEFT JOIN nmv__experiment_foi ef ON ef.ID_experiment = e.ID_experiment
																		LEFT JOIN nmv__field_of_interest foi ON foi.ID_foi = ef.ID_foi
																		WHERE v.mpg_project = -1
																		GROUP BY ef.ID_foi) B
														      ON A.english = B.english';
    $querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']}";

		// execute query
		$query_items = $dbi->connection->query($querystring_items.$querystring_orderby);

$layout
	->set('title','Experiment (Field of Interest)');
$layout
	->set('content',
          '<p><br>
						Number of people who where victim of experiments with certain fields of interest.
						<br> <strong>One person can be victim of different experiments with different fields of interest.
						<br> Not all victims are assigned to an experiment.</strong>
						<br> The total number in this table therefore does not correspond to the number of victims in the database.
					</p>'
					// Tabelle bauen
					.$dbi->getListView('statistics_experiment_foi_table',$query_items)
          .'<br><br>'
          .createButton('Back','javascript:history.back()')
          .createButton('Forward', 'javascript:history.forward()')
        );
require_once 'statistics_navigation.php'; // navigation to different statistics
$layout->cast();

?>
