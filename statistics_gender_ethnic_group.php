<?php
/**
*creates statistics for gender / ethnic group
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

//Query gender /ethnic group
$querystring_items = '	SELECT A.ethnic_group as ethnic_group, A.gender as gender, A.number as anumber, B.number as bnumber
												FROM	(SELECT e.english as ethnic_group, v.gender as gender, COUNT(v.ID_victim) as number
												        FROM nmv__victim v
												        LEFT JOIN nmv__ethnic_group e on e.ID_ethnic_group = v.ID_ethnic_group
																WHERE was_prisoner_assistant != "prisoner assistant only"
																GROUP BY e.english, v.gender) A
												      LEFT JOIN
												      	(SELECT e.english as ethnic_group, v.gender as gender, COUNT(v.ID_victim) as number
												        FROM nmv__victim v
												        LEFT JOIN nmv__ethnic_group e on e.ID_ethnic_group = v.ID_ethnic_group
												        WHERE v.mpg_project = -1 AND was_prisoner_assistant != "prisoner assistant only"
												        GROUP BY e.english, v.gender) B
												      ON A.ethnic_group = B.ethnic_group AND A.gender = B.gender
															OR ((A.gender IS NULL AND B.gender IS NULL) AND (A.ethnic_group IS NULL AND B.ethnic_group IS NULL))
															OR ((A.gender = B.gender) AND (A.ethnic_group IS NULL AND B.ethnic_group IS NULL))
															OR ((A.gender IS NULL AND B.gender IS NULL) AND (A.ethnic_group = B.ethnic_group))';
$querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']}";

// execute query
$query_items = $dbi->connection->query($querystring_items.$querystring_orderby);

$layout
	->set('title','Statistic - Gender - Ethnic Group');
$layout
	->set('content',
          '<br>'
					// Tabelle bauen
					.$dbi->getListView('statistics_gender_ethnic_group_table',$query_items)
          .'<br><br>'
          .createButton('Back','javascript:history.back()')
          .createButton('Forward', 'javascript:history.forward()')
        );
require_once 'statistics_navigation.php'; // navigation to different statistics
$layout->cast();

?>
