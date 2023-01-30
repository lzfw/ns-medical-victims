<?php
/**
*creates statistics for prisoner classification
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
$dbi->setUserVar ('sort',getUrlParameter('sort'),'classification');
$dbi->setUserVar ('order',getUrlParameter('order'),'ASC');



		//query
		$querystring_items = ' 	SELECT A.classification, A.number as anumber, B.number as bnumber
														FROM (SELECT c.classification, COUNT(v.ID_victim) as number
																FROM nmv__victim v
																INNER JOIN nmv__imprisonment i
															      ON v.ID_victim = i.ID_victim
																LEFT JOIN nmv__imprisonment_classification ic
																		ON ic.ID_imprisonment = i.ID_imprisonment
																LEFT JOIN nmv__victim_classification c
															      ON ic.ID_classification = c.ID_classification
																WHERE was_prisoner_assistant != "prisoner assistant only"
																GROUP BY c.classification) A
														  LEFT JOIN
															    (SELECT c.classification, COUNT(v.ID_victim) as number
																FROM nmv__victim v
																INNER JOIN nmv__imprisonment i
															      ON v.ID_victim = i.ID_victim
																LEFT JOIN nmv__imprisonment_classification ic
																		ON ic.ID_imprisonment = i.ID_imprisonment
																LEFT JOIN nmv__victim_classification c
															      ON ic.ID_classification = c.ID_classification
																WHERE v.mpg_project = -1 AND was_prisoner_assistant != "prisoner assistant only"
																GROUP BY c.classification) B
														  ON A.classification = B.classification OR (A.classification IS NULL AND B.classification IS NULL)';
    $querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']}";

    //execute Query
		$query_items = $dbi->connection->query($querystring_items.$querystring_orderby);

$layout
	->set('title','Statistic - Prisoner Classification');
$layout
	->set('content',
          '<br><p>Most categories are based on classifications by the location of imprisonment.
					<br> The numbers must be used carefully:
					<br> Some victims have different classifications over time.
					<br> Victims who were not imprisoned in the narrow sense are not included here.</p>'
					// Tabelle bauen
					.$dbi->getListView('statistics_classification_table', $query_items)
          .'<br><br>'
          .createButton('Back','javascript:history.back()')
          .createButton('Forward', 'javascript:history.forward()')
        );
require_once 'statistics_navigation.php'; // navigation to different statistics
$layout->cast();

?>
