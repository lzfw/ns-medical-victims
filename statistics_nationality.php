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
$dbi->setUserVar ('sort',getUrlParameter('sort'),'nationality');
$dbi->setUserVar ('order',getUrlParameter('order'),'ASC');

//Query gender / nationality
$querystring_items = 'SELECT A.nationality as nationality, A.number as anumber, B.number as bnumber
                      FROM	(SELECT n.english as nationality, COUNT(v.ID_victim) as number
                              FROM nmv__victim v
                              LEFT JOIN nmv__nationality n on v.ID_nationality_1938 = n.ID_nationality
                              WHERE v.was_prisoner_assistant != "prisoner assistant only"
                              GROUP BY n.english) A
                            LEFT JOIN
                            	(SELECT n.english as nationality, COUNT(v.ID_victim) as number
                              FROM nmv__victim v
                              LEFT JOIN nmv__nationality n on v.ID_nationality_1938 = n.ID_nationality
                              WHERE v.mpg_project = -1 AND v.was_prisoner_assistant != "prisoner assistant only"
                              GROUP BY n.english) B
                            ON A.nationality = B.nationality OR (A.nationality IS NULL AND B.nationality IS NULL)';
$querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']}";

// execute query
$query_items = $dbi->connection->query($querystring_items.$querystring_orderby);

$layout
	->set('title','Statistic - Nationality');
$layout
	->set('content',
          '<br>'
					// Tabelle bauen
					.$dbi->getListView('statistics_nationality_table',$query_items)
          .'<br><br>'
          .createButton('Back','javascript:history.back()')
          .createButton('Forward', 'javascript:history.forward()')
        );
require_once 'statistics_navigation.php'; // navigation to different statistics
$layout->cast();

?>
