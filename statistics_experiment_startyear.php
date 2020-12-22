<?php
/**
*creates statistics for gender / nationality
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
$dbi->setUserVar ('sort',getUrlParameter('sort'),'startyear');
$dbi->setUserVar ('order',getUrlParameter('order'),'ASC');

//Query gender / nationality
$querystring_items = 'SELECT A.startyear as startyear, A.number as anumber, B.number as bnumber
                      FROM 	(SELECT ve.exp_start_year as startyear, COUNT(v.ID_victim) as number
                      		  FROM nmv__victim v
                      		  INNER JOIN nmv__victim_experiment ve ON v.ID_victim = ve.ID_victim
                            GROUP BY ve.exp_start_year) A

                           LEFT JOIN

                           	(SELECT ve.exp_start_year as startyear, COUNT(v.ID_victim) as number
                      		  FROM nmv__victim v
                      		  INNER JOIN nmv__victim_experiment ve ON v.ID_victim = ve.ID_victim
                            WHERE v.mpg_project = -1
                            GROUP BY ve.exp_start_year) B

                           ON A.startyear = B.startyear OR (A.startyear IS NULL AND B.startyear IS NULL)';
$querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']}";

// execute query
$query_items = $dbi->connection->query($querystring_items.$querystring_orderby);

$layout
	->set('title','Statistic - Experiment (Startyear)');
$layout
	->set('content',
          '<p><br>
						Numbers of research victims by year the research started.
						<br> <strong>One person can be victim of different experiments with different startyears.
						<br> Not all victims are assigned to an experiment.</strong>
						<br> The total number in this table therefore does not correspond to the number of victims in the database.
					</p>'
					// Tabelle bauen
					.$dbi->getListView('statistics_experiment_startyear_table',$query_items)
          .'<br><br>'
          .createButton('Back','javascript:history.back()')
          .createButton('Forward', 'javascript:history.forward()')
        );
require_once 'statistics_navigation.php'; // navigation to different statistics
$layout->cast();

?>
