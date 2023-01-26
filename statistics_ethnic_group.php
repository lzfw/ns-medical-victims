<?php
/**
*creates statistics for ethnic groups
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
$dbi->setUserVar ('sort',getUrlParameter('sort'),'ethnic_group');
$dbi->setUserVar ('order',getUrlParameter('order'),'ASC');


//Query ethnic group
		//query
		$querystring_items = ' 	SELECT A.ethnic_group as ethnic_group, A.number as anumber, B.number as bnumber
														FROM    (SELECT COUNT(v.ID_victim) as number, e.english as ethnic_group
														        FROM nmv__victim v
														        LEFT JOIN nmv__ethnic_group e ON v.ID_ethnic_group = e.ID_ethnic_group
																		WHERE v.was_prisoner_assistant != "prisoner assistant only"
														        GROUP BY v.ID_ethnic_group) A
														      LEFT JOIN
														        (SELECT COUNT(v.ID_victim) as number, e.english as ethnic_group
														        FROM nmv__victim v
														        LEFT JOIN nmv__ethnic_group e ON v.ID_ethnic_group = e.ID_ethnic_group
														        WHERE v.mpg_project = -1 AND v.was_prisoner_assistant != "prisoner assistant only"
														        GROUP BY v.ID_ethnic_group) B
														      ON A.ethnic_group = B.ethnic_group OR (A.ethnic_group IS NULL AND B.ethnic_group IS NULL)';
    $querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']}";

    //execute Query
		$query_items = $dbi->connection->query($querystring_items.$querystring_orderby);


$layout
	->set('title','Statistic - Ethnic Group');
$layout
	->set('content',
          '<br>'
					// Tabelle bauen
					.$dbi->getListView('statistics_ethnic_group_table', $query_items)
          .'<br><br>'
          .createButton('Back','javascript:history.back()')
          .createButton('Forward', 'javascript:history.forward()')
        );
require_once 'statistics_navigation.php'; // navigation to different statistics
$layout->cast();

?>
