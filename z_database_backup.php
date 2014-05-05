<?php
// CMS file: database management
// last known update: 2014-01-27

require_once 'zefiro/ini.php';
require_once 'flotilla/lib/opendb_mysql.php';

$dbi->requireUserPermission ('admin');

openDB ($db_host,$db_user,$db_pass,$db_name);

// template variables
$template_title = '';
$template_content = '';
$template_sidebar = '';

// title
$template_title = DBI_DATABASE_BACKUP;

// breadcrumbs
$dbi->addBreadcrumb (DBI_ADMIN,'z_admin');
$dbi->addBreadcrumb (DBI_DATABASE,'z_database');
$dbi->addBreadcrumb (DBI_DATABASE_BACKUP);

// user action
$action = getUrlParameter('action');

switch ($action) {

	case 'backup':
		//$filename = 'backup/database_'.date("Y-m-d_H-i-s").'.sql';
		$backup = mysql_backup('abbreviations,arabic_letters,arabic_pos,zo_helptexts,zo_textblocks,filecards,glossary,greek_pos,links,sources');
		header('Content-Disposition: attachment; filename=gga_backup.sql');
		header('Content-type: application/force-download');
		echo $backup;
		exit;
	break;
	
	default:
		$template_content .= $dbi->getHelptext_HTML ('database_backup');
		$template_content .= '<div class="buttons">';
		$template_content .= createButton (DBI_NO_THANKS,'zo_database.php','icon no');
		$template_content .= createButton (DBI_DOWNLOAD,'zo_database_backup.php?action=backup','icon download');
		$template_content .= '</div>';
	break;

}

// call template
require_once 'templates/ini.php';

?>
