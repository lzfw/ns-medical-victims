<?php
// CMS file: database management
// last known update: 2013-01-22

require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('admin');

// url parameters
$task = isset($_GET['task'])?$_GET['task']:NULL;

// breadcrumbs
$dbi->addBreadcrumb (Z_ADMIN,'z_admin.php');
$dbi->addBreadcrumb (Z_DATABASE,'z_database.php');
$dbi->addBreadcrumb (Z_DATABASE_MAINTENANCE,'z_database_maintenance.php');

$template_content = '';
// task
switch ($task) {

	case 'optimize':
		// title
		$template_title = Z_DATABASE_OPTIMIZE;
		// breadcrumbs
		$dbi->addBreadcrumb (Z_DATABASE_OPTIMIZE);
		// content
		$optimize_qs = "
			OPTIMIZE TABLE
				`z_helptexts`,
				`z_textblocks`,
				`z_users`,
				`abbreviations`,
				`arabic_letters`,
				`arabic_pos`,
				`filecards`,
				`glossary`,
				`greek_pos`,
				`links`,
				`sources`
		";
		$optimize_q = $dbi->connection->query($optimize_qs);
		while ($row = $optimize_q->fetch_row()) {
			$template_content .= '<p>'.$row[0].': '.$row[1].' / '.$row[2].': '.$row[3].'</p>'.PHP_EOL;
		}
		$template_content .= '<p>'.createButton (Z_OK,'z_database_maintenance.php','icon ok').'</p>'.PHP_EOL;
	break;

	// CUSTOM FUNCTIONS -------------------------------------------------------

	case 'completeWordSources':
		// title
		$template_title = GGA_COMPLETE_MISSING_SOURCES;
		// breadcrumbs
		$dbi->addBreadcrumb (GGA_COMPLETE_MISSING_SOURCES);
		// content
		$template_content .= '<p>';
		$template_content .= 'Wörter mit erschließbarer Source-Referenz einlesen ... ';
		$words_querystring = "
			SELECT g.word_id, f.source_id
			FROM glossary g
			LEFT OUTER JOIN filecards f USING (filecard_id)
			WHERE g.source_id = 0 AND g.filecard_id <> 0 AND f.source_id <> 0
		";
		$words_query = $dbi->connection->query($words_querystring);
		$template_content .= $words_query->num_rows.'</p>';
		while ($word = $words_query->fetch_object()) {
			echo $update_querystring = '
				UPDATE glossary g
				SET (g.source_id)
				VALUES ('.$word->source_id.')
				WHERE g.word_id='.$word->word_id.'</br>';
		}
		$template_content .= '<div class="buttons">';
		$template_content .= createButton (Z_OK,'z_database_maintenance.php','icon ok');
		$template_content .= '</div>';
	break;

	// END CUSTOM FUNCTIONS ---------------------------------------------------

	default:
		// title
		$template_title = Z_DATABASE_MAINTENANCE;
		// content
		$template_content .= '<ul class="icons">';
		$template_content .= createListItem(Z_DATABASE_OPTIMIZE,'z_database_maintenance.php?task=optimize','dbStatus');
		$template_content .= createListItem('[custom maintenance task]','z_database_maintenance.php?task=task_id','dbStatus');
		$template_content .= '</ul>';
		$template_content .= createBackLink (Z_DATABASE,'z_database.php');
	break;

}

// sidebar
$template_sidebar = '';

// call template
require_once 'templates/ini.php';

?>
