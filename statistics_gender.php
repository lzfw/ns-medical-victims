<?php
/**
*creates statistics for gender
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
$dbi->setUserVar ('sort',getUrlParameter('sort'),'gender');
$dbi->setUserVar ('order',getUrlParameter('order'),'ASC');



//Query gender
$querystring_items = 'SELECT A.gender as gender, A.number as anumber, B.number as bnumber
											FROM 	(SELECT COUNT(v.ID_victim) as number, v.gender as gender
														FROM nmv__victim v
														GROUP BY v.gender) A
													LEFT JOIN
														(SELECT COUNT(v.ID_victim) as number, v.gender as gender
														FROM nmv__victim v
														WHERE mpg_project = -1
														GROUP BY v.gender) B
													ON A.gender = B.gender OR (A.gender IS NULL AND B.gender IS NULL)';
$querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']}";


// execute query
$query_items = $dbi->connection->query($querystring_items.$querystring_orderby);


$layout
	->set('title','Statistic - Gender');
$layout
	->set('content',
          '<br>'
					// Tabelle bauen
					.$dbi->getListView('statistics_gender_table',$query_items)
          .'<br><br>'
          .createButton('Back','javascript:history.back()')
          .createButton('Forward', 'javascript:history.forward()')
        );
require_once 'statistics_navigation.php'; // navigation to different statistics
$layout->cast();

?>
