<?php
/**
*creates statistics for ethnicgroups
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
$dbi->setUserVar ('sort',getUrlParameter('sort'),'ethnicgroup');
$dbi->setUserVar ('order',getUrlParameter('order'),'ASC');


//Query ethnicgroup
		//query
		$querystring_items = ' 	SELECT A.ethnicgroup as ethnicgroup, A.number as anumber, B.number as bnumber
														FROM    (SELECT COUNT(v.ID_victim) as number, e.english as ethnicgroup
														        FROM nmv__victim v
														        LEFT JOIN nmv__ethnicgroup e ON v.ethnic_group = e.ID_ethnicgroup
																		WHERE v.was_prisoner_assistant != "prisoner assistant only"
														        GROUP BY v.ethnic_group) A
														      LEFT JOIN
														        (SELECT COUNT(v.ID_victim) as number, e.english as ethnicgroup
														        FROM nmv__victim v
														        LEFT JOIN nmv__ethnicgroup e ON v.ethnic_group = e.ID_ethnicgroup
														        WHERE v.mpg_project = -1 AND v.was_prisoner_assistant != "prisoner assistant only"
														        GROUP BY v.ethnic_group) B
														      ON A.ethnicgroup = B.ethnicgroup OR (A.ethnicgroup IS NULL AND B.ethnicgroup IS NULL)';
    $querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']}";

    //execute Query
		$query_items = $dbi->connection->query($querystring_items.$querystring_orderby);


$layout
	->set('title','Statistic - Ethnicgroup');
$layout
	->set('content',
          '<br>'
					// Tabelle bauen
					.$dbi->getListView('statistics_ethnicgroup_table', $query_items)
          .'<br><br>'
          .createButton('Back','javascript:history.back()')
          .createButton('Forward', 'javascript:history.forward()')
        );
require_once 'statistics_navigation.php'; // navigation to different statistics
$layout->cast();

?>
