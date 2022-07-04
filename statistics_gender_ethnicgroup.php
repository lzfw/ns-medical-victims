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
$dbi->setUserVar ('sort',getUrlParameter('sort'),'ethnicgroup');
$dbi->setUserVar ('order',getUrlParameter('order'),'ASC');

//Query gender /ethnicgroup
$querystring_items = '	SELECT A.ethnicgroup as ethnicgroup, A.gender as gender, A.number as anumber, B.number as bnumber
												FROM	(SELECT e.english as ethnicgroup, v.gender as gender, COUNT(v.ID_victim) as number
												        FROM nmv__victim v
												        LEFT JOIN nmv__ethnicgroup e on e.ID_ethnicgroup = v.ethnic_group
																WHERE was_prisoner_assistant != "prisoner assistant only"
																GROUP BY e.english, v.gender) A
												      LEFT JOIN
												      	(SELECT e.english as ethnicgroup, v.gender as gender, COUNT(v.ID_victim) as number
												        FROM nmv__victim v
												        LEFT JOIN nmv__ethnicgroup e on e.ID_ethnicgroup = v.ethnic_group
												        WHERE v.mpg_project = -1 AND was_prisoner_assistant != "prisoner assistant only"
												        GROUP BY e.english, v.gender) B
												      ON A.ethnicgroup = B.ethnicgroup AND A.gender = B.gender
															OR ((A.gender IS NULL AND B.gender IS NULL) AND (A.ethnicgroup IS NULL AND B.ethnicgroup IS NULL))
															OR ((A.gender = B.gender) AND (A.ethnicgroup IS NULL AND B.ethnicgroup IS NULL))
															OR ((A.gender IS NULL AND B.gender IS NULL) AND (A.ethnicgroup = B.ethnicgroup))';
$querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']}";

// execute query
$query_items = $dbi->connection->query($querystring_items.$querystring_orderby);

$layout
	->set('title','Statistic - Gender - Ethnic Group');
$layout
	->set('content',
          '<br>'
					// Tabelle bauen
					.$dbi->getListView('statistics_gender_ethnicgroup_table',$query_items)
          .'<br><br>'
          .createButton('Back','javascript:history.back()')
          .createButton('Forward', 'javascript:history.forward()')
        );
require_once 'statistics_navigation.php'; // navigation to different statistics
$layout->cast();

?>
