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
$dbi->setUserVar ('sort',getUrlParameter('sort'),'nationality, gender');
$dbi->setUserVar ('order',getUrlParameter('order'),'ASC');

//Query gender / nationality
$querystring_items = '	SELECT A.nationality as nationality, A.gender as gender, A.number as anumber, B.number as bnumber
												FROM	(SELECT n.english as nationality, v.gender as gender, COUNT(v.ID_victim) as number
												        FROM nmv__victim v
												        LEFT JOIN nmv__nationality n on v.nationality_1938 = n.ID_nationality
																WHERE was_prisoner_assistant != "prisoner assistant only"
												        GROUP BY n.english, v.gender) A
												      LEFT JOIN
												      	(SELECT n.english as nationality, v.gender as gender, COUNT(v.ID_victim) as number
												        FROM nmv__victim v
												        LEFT JOIN nmv__nationality n on v.nationality_1938 = n.ID_nationality
												        WHERE v.mpg_project = -1 AND was_prisoner_assistant != "prisoner assistant only"
												        GROUP BY n.english, v.gender) B
												      ON A.nationality = B.nationality AND A.gender = B.gender
															OR ((A.gender IS NULL AND B.gender IS NULL) AND (A.nationality IS NULL AND B.nationality IS NULL))
															OR ((A.gender = B.gender) AND (A.nationality IS NULL AND B.nationality IS NULL))
															OR ((A.gender IS NULL AND B.gender IS NULL) AND (A.nationality = B.nationality))';
$querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']}";

// execute query
$query_items = $dbi->connection->query($querystring_items.$querystring_orderby);

$layout
	->set('title','Statistic - Gender - Nationality');
$layout
	->set('content',
          '<br>'
					// Tabelle bauen
					.$dbi->getListView('statistics_gender_nationality_table',$query_items)
          .'<br><br>'
          .createButton('Back','javascript:history.back()')
          .createButton('Forward', 'javascript:history.forward()')
        );
require_once 'statistics_navigation.php'; // navigation to different statistics
$layout->cast();

?>
