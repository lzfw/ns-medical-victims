<?php
// CMS file: database management
// last known update: 2013-01-22

require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('admin');

// url parameters
$task = isset($_GET['task'])?$_GET['task']:NULL;

// breadcrumbs
$dbi->addBreadcrumb (L_ADMIN,'z_admin.php');
$dbi->addBreadcrumb (L_DATABASE,'z_database.php');
$dbi->addBreadcrumb (L_DATABASE_MAINTENANCE,'z_database_maintenance.php');

$template_content = '';
// task
switch ($task) {

	case 'optimize':
		// title
		$template_title = L_DATABASE_OPTIMIZE;
		// breadcrumbs
		$dbi->addBreadcrumb (L_DATABASE_OPTIMIZE);
		// content
		$optimize_qs = "
			OPTIMIZE TABLE
				`z_helptexts`,
				`z_textblocks`,
				`z_users`
		";
		$optimize_q = $dbi->connection->query($optimize_qs);
		while ($row = $optimize_q->fetch_row()) {
			$template_content .= '<p>'.$row[0].': '.$row[1].' / '.$row[2].': '.$row[3].'</p>'.PHP_EOL;
		}
		$template_content .= '<p>'.createButton (Z_OK,'z_database_maintenance.php','icon ok').'</p>'.PHP_EOL;
	break;

	// CUSTOM FUNCTIONS -------------------------------------------------------

	// a lot of space for customization
	
	// END CUSTOM FUNCTIONS ---------------------------------------------------

	default:
		// title
		$template_title = L_DATABASE_MAINTENANCE;
		// content
		$template_content .= '<ul class="icons">';
		$template_content .= createListItem(L_DATABASE_OPTIMIZE,'z_database_maintenance.php?task=optimize','dbStatus');
		$template_content .= createListItem('[custom maintenance task]','z_database_maintenance.php?task=task_id','dbStatus');
		$template_content .= '</ul>';
		$template_content .= createBackLink (L_DATABASE,'z_database.php');
	break;

}

// sidebar
$template_sidebar = '';

// call template
require_once 'templates/ini.php';

?>
