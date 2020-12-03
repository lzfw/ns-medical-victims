<?php
/**
*creates statistics page
*
*
*
*/


require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';
$dbi->requireUserPermission ('view');



// build page html
$layout
	->set('title','Statistics')
	->set('content', '
					<p>The statitics are based on the current state of the database.
						<br>To open the different statistics, please use the navigation on the right sidebar.</p>
				');
require_once 'statistics_navigation.php';
$layout->cast();

?>
