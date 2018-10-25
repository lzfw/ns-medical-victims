<?php
// CMS file: database management
// last known update: 2013-01-22

require_once 'zefiro/ini.php';
require_once 'flotilla/lib/opendb_mysql.php';

$dbi->requireUserPermission ('system');

// url parameters
$user_action = getUrlParameter('action');
$user_file = getUrlParameter('file');
$user_confirm = getUrlParameter('confirm');

// template variables
$template_title = '';
$template_content = '';
$template_sidebar = '';

// title
$template_title .= L_DATABASE_RECOVERY;

// breadcrumbs
$dbi->addBreadcrumb (L_ADMIN,'z_admin.php');
$dbi->addBreadcrumb (L_DATABASE,'z_database.php');
$dbi->addBreadcrumb (L_DATABASE_RECOVERY);

// database connection
openDB ($db_host,$db_user,$db_pass,$db_name);

// name pattern of backup file
$suchmuster = '/database\_(\d+)-(\d+)-(\d+)\_(\d+)-(\d+)-(\d+)\.sql/i';
$ersetzung = '<b>$3.$2.$1</b> um <b>$4:$5:$6</b>';

// user action
switch ($user_action) {

	case 'recover':
		switch ($user_confirm) {
			case 'yes':
				/* TODO dies ist durch CREATE TABLE usw. zu ersetzen ... */
				// recovery confirmed
				$filename = 'backup/'.$user_file;
				$command = $mysqldir.'mysql -h '.$db_host.' -u '.$db_user.' -p"'.$db_pass.'" glossga < '.$filename;
				if (system($command)) {
					$template_content .= '<p>'.L_DATABASE_RECOVERY_OK.'</p>';
				} else {
					$template_content .= '<p>'.L_ERROR_ABORTED.'</p>';
				}
				$template_content .= '<div class="buttons">';
				$template_content .= createButton (L_OK,'database.php',ICON_OK);
				$template_content .= '</div>';
			break;
			case 'no':
				// recovery cancelled
				header('Location: database_recovery.php');
			break;
			default:
				// recovery must be confirmed
				$template_content .= '<p>'.L_ARE_YOU_SURE.'</p>';
				$template_content .= '<div class="buttons">';
				$template_content .= createButton (L_NO,'z_database_recovery.php?action=recover&file='.$user_file.'&confirm=no','icon no');
				$template_content .= createButton (L_YES,'z_database_recovery.php?action=recover&file='.$user_file.'&confirm=yes','icon yes');
				$template_content .= '</div>';
			break;
		}
	break;
	
	case 'remove':
		switch ($user_confirm) {
			case 'yes':
				// deletion confirmed
				unlink ('backup/'.$user_file);
				header('Location: z_database_recovery.php');
			break;
			case 'no':
				// deletion cancelled
				header('Location: z_database_recovery.php');
			break;
			default:
				// deletion must be confirmed
				$template_content .= '<p>'.L_ARE_YOU_SURE.'</p>';
				$template_content .= '<div class="buttons">';
				$template_content .= createButton (L_NO,'z_database_recovery.php?action=remove&file='.$user_file.'&confirm=no','icon no');
				$template_content .= createButton (L_YES,'z_database_recovery.php?action=remove&file='.$user_file.'&confirm=yes','icon yes');
				$template_content .= '</div>';
			break;
		}
	break;

	default:
		$template_content .= $dbi->getHelptext_HTML ('database_recovery');
		$template_content .= '<ul>';
		if ($handle = opendir('backup')) {
			while (false !== ($file = readdir($handle))) {
				if ($file != '.' && $file != '..') {
					$template_content .= '<li>'.preg_replace($suchmuster, $ersetzung, $file).' '.
						createSmallButton(L_RECOVER,'database_recovery.php?action=recover&file='.$file,'icon recover').
						createSmallButton(L_DELETE,'database_recovery.php?action=remove&file='.$file,'icon delete');
				}
			}
			closedir($handle);
		}
		$template_content .= '</ul>';
		$template_content .= '<div class="buttons">';
		$template_content .= createButton (L_OK,'z_database.php','icon ok');
		$template_content .= '</div>';
	break;
}

// call template
require_once 'templates/ini.php';

?>
