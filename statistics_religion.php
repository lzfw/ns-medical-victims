<?php
/**
*creates statistics for religions
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
$dbi->setUserVar ('sort',getUrlParameter('sort'),'religion');
$dbi->setUserVar ('order',getUrlParameter('order'),'ASC');



//Query religion
$querystring_items = 'SELECT A.religion as religion, A.number as anumber, B.number as bnumber
											FROM (SELECT COUNT(v.ID_victim) as number, r.religion
											  		FROM nmv__victim v
											  		LEFT JOIN nmv__religion r ON v.ID_religion = r.ID_religion
														WHERE v.was_prisoner_assistant != "prisoner assistant only"
											  		GROUP BY v.ID_religion
											  		) A
														LEFT JOIN
											  		(SELECT COUNT(v.ID_victim) as number, r.religion
											  		FROM nmv__victim v
											  		LEFT JOIN nmv__religion r ON v.ID_religion = r.ID_religion
											  		WHERE v.mpg_project = -1 AND v.was_prisoner_assistant != "prisoner assistant only"
											  		GROUP BY v.ID_religion
											    	) B
														ON A.religion = B.religion OR (A.religion IS NULL AND B.religion IS NULL)
											';
$querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']}";

// execute query
$query_items = $dbi->connection->query($querystring_items.$querystring_orderby);


$layout
	->set('title','Statistic - Religion');
$layout
	->set('content',
          '<br>'
					// Tabelle bauen
					.$dbi->getListView('statistics_religion_table',$query_items)
          .'<br><br>'
          .createButton('Back','javascript:history.back()')
          .createButton('Forward', 'javascript:history.forward()')
        );
require_once "statistics_navigation.php";  // navigation to different statistics
$layout->cast();

?>
