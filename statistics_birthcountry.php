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


// url parameters
$dbi->setUserVar ('view',getUrlParameter('view'),'default');
// browsing options
$dbi->setUserVar ('sort',getUrlParameter('sort'),'birthcountry');
$dbi->setUserVar ('order',getUrlParameter('order'),'ASC');

//Query gender / nationality
$querystring_items = 'SELECT A.birthcountry as birthcountry, A.number as anumber, B.number as bnumber
                      FROM	(SELECT c.country as birthcountry, COUNT(v.ID_victim) as number
                              FROM nmv__victim v
                              LEFT JOIN nmv__country c on v.ID_birth_country = c.ID_country
                              WHERE v.was_prisoner_assistant != "prisoner assistant only"
                              GROUP BY c.country) A
                            LEFT JOIN
                            	(SELECT c.country as birthcountry, COUNT(v.ID_victim) as number
                              FROM nmv__victim v
                              LEFT JOIN nmv__country c on v.ID_birth_country = c.ID_country
                              WHERE v.mpg_project = -1 AND v.was_prisoner_assistant != "prisoner assistant only"
                              GROUP BY c.country) B
                            ON A.birthcountry = B.birthcountry OR (A.birthcountry IS NULL AND B.birthcountry IS NULL)';
$querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']}";

// execute query
$query_items = $dbi->connection->query($querystring_items.$querystring_orderby);

$layout
	->set('title','Statistic - Country of Birth');
$layout
	->set('content',
          '<br>'
					// Tabelle bauen
					.$dbi->getListView('statistics_birthcountry_table',$query_items)
          .'<br><br>'
          .createButton('Back','javascript:history.back()')
          .createButton('Forward', 'javascript:history.forward()')
        );
require_once 'statistics_navigation.php'; // navigation to different statistics
$layout->cast();

?>
