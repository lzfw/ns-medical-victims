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
$dbi->setUserVar ('sort',getUrlParameter('sort'),'survival');
$dbi->setUserVar ('order',getUrlParameter('order'),'ASC');

//Query gender / nationality
$querystring_items = 'SELECT s.survival, A.survival as survivalID, A.number as anumber, B.number as bnumber
                      FROM
                        (SELECT COUNT(v.ID_victim) as number, t.survivalID as survival
                        FROM nmv__victim v
                        LEFT JOIN (
                            SELECT ve.ID_victim, MAX(ve.ID_survival) AS survivalID
                        	  FROM nmv__victim_experiment ve
                        	  GROUP BY ve.ID_victim) as t
                        ON v.ID_victim = t.ID_victim
                        GROUP BY t.survivalID) A

                      LEFT JOIN

                        (SELECT COUNT(v.ID_victim) as number, t.survivalID as survival
                        FROM nmv__victim v
                        LEFT JOIN (
                            SELECT ve.ID_victim, MAX(ve.ID_survival) AS survivalID
                        	  FROM nmv__victim_experiment ve
                        	  GROUP BY ve.ID_victim) as t
                        ON v.ID_victim = t.ID_victim
                        WHERE v.mpg_project = -1
                        GROUP BY t.survivalID) B

                      ON A.survival = B.survival OR (A.survival IS NULL AND B.survival IS NULL)
                      LEFT JOIN nmv__survival s ON s.ID_survival = A.survival
                      ';
$querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']}";

//TODO additional query, in case every survival-entry shall be displayed
// SELECT A.survival as survival, A.ID_survival as survivalID, A.number as anumber, B.number as bnumber
// FROM (SELECT COUNT(v.ID_victim) AS number, s.survival, ve.ID_survival
//     FROM nmv__victim v
//     LEFT JOIN nmv__victim_experiment ve ON v.ID_victim = ve.ID_victim
//     LEFT JOIN nmv__survival s ON s.ID_survival = ve.ID_survival
//     GROUP BY ve.ID_survival) A
//   LEFT JOIN
//   	(SELECT COUNT(v.ID_victim) AS number, s.survival, ve.ID_survival
//     FROM nmv__victim v
//     LEFT JOIN nmv__victim_experiment ve ON v.ID_victim = ve.ID_victim
//     LEFT JOIN nmv__survival s ON s.ID_survival = ve.ID_survival
//     WHERE v.mpg_project = -1
//     GROUP BY ve.ID_survival) B
//   ON A.ID_survival = B.ID_survival

// execute query
$query_items = $dbi->connection->query($querystring_items.$querystring_orderby);

$layout
	->set('title','Statistic - Survival');
$layout
	->set('content',
          '<br>
          <p>Some persons where victim of more than one experiment. They can have different survival-values for every experiment.
          <br>
          In this table only one of these survival-values is used for every victim. <br>
          (for example: a victim survived the first experiment, but not the second one -> only the survival-value of the second experiment is counted)
          <br>
          In ascending priority: "survived", "died in experiment (procedures)", "killed after experiment", "died later in camp", "died of injuries/aftermath", "body used after death", "killed for an experiment"
          </p>'
					// Tabelle bauen
					.$dbi->getListView('statistics_survival_table',$query_items)
          .'<br><br>'
          .createButton('Back','javascript:history.back()')
          .createButton('Forward', 'javascript:history.forward()')
        );
require_once 'statistics_navigation.php'; // navigation to different statistics
$layout->cast();

?>
