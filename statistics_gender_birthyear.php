<?php
/**
*creates statistics for gender / birthyear
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
$dbi->setUserVar ('sort',getUrlParameter('sort'),'birth_year');
$dbi->setUserVar ('order',getUrlParameter('order'),'ASC');

//Query gender / year_of_birth
$querystring_items = '	SELECT A.birth_year as birth_year, A.gender as gender, A.number as anumber, B.number as bnumber
												FROM 	(SELECT v.birth_year as birth_year, v.gender as gender, COUNT(v.ID_victim) as number
														FROM nmv__victim v
														GROUP BY v.birth_year, v.gender) A
												      LEFT JOIN
												      	(SELECT v.birth_year as birth_year, v.gender as gender, COUNT(v.ID_victim) as number
																FROM nmv__victim v
												        WHERE v.mpg_project = -1
														GROUP BY v.birth_year, v.gender) B
												      ON A.birth_year = B.birth_year AND A.gender = B.gender';
$querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']}";

// execute query
$query_items = $dbi->connection->query($querystring_items.$querystring_orderby);


$layout
	->set('title','Statistic - Gender - Year of Birth');
$layout
	->set('content',
          '<br>'
					// Tabelle bauen
					.$dbi->getListView('statistics_gender_birthyear_table',$query_items)
          .'<br><br>'
          .createButton('Back','javascript:history.back()')
          .createButton('Forward', 'javascript:history.forward()')
        );
require_once 'statistics_navigation.php'; // navigation to different statistics
$layout->cast();

?>
