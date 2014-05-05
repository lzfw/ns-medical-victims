<?php
// CMS file: database management
// last known update: 2013-01-22

require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('admin');

// url parameters
$task = isset($_GET['task'])?$_GET['task']:NULL;

// breadcrumbs
$dbi->addBreadcrumb (DBI_ADMIN,'zo_admin.php');
$dbi->addBreadcrumb (DBI_DATABASE,'zo_database.php');
$dbi->addBreadcrumb (DBI_DATABASE_MAINTENANCE,'zo_database_maintenance.php');

$template_content = '';
// task
switch ($task) {
	
	case 'optimize':
		// title
		$template_title = DBI_DATABASE_OPTIMIZE;
		// breadcrumbs
		$dbi->addBreadcrumb (DBI_DATABASE_OPTIMIZE);
		// content
		$optimize_qs = "
			OPTIMIZE TABLE
				`zo_helptexts`,
				`zo_textblocks`,
				`zo_users`,
				`abbreviations`,
				`arabic_letters`,
				`arabic_pos`,
				`filecards`,
				`glossary`,
				`greek_pos`,
				`links`,
				`sources`
		";
		$optimize_q = mysql_query($optimize_qs);
		while ($row = mysql_fetch_row($optimize_q)) {
			$template_content .= '<p>'.$row[0].': '.$row[1].' / '.$row[2].': '.$row[3].'</p>'.PHP_EOL;
		}
		$template_content .= '<p>'.createButton (DBI_OK,'zo_database_maintenance.php','icon ok').'</p>'.PHP_EOL;
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
		$words_query = mysql_query($words_querystring);
		$template_content .= mysql_num_rows($words_query).'</p>';
		while ($word = mysql_fetch_object ($words_query)) {
			echo $update_querystring = '
				UPDATE glossary g
				SET (g.source_id)
				VALUES ('.$word->source_id.')
				WHERE g.word_id='.$word->word_id.'</br>';
		}
		$template_content .= '<div class="buttons">';
		$template_content .= createButton (DBI_OK,'zo_database_maintenance.php','icon ok');
		$template_content .= '</div>';
	break;
	
	// END CUSTOM FUNCTIONS ---------------------------------------------------
	
	default:
		// title
		$template_title = DBI_DATABASE_MAINTENANCE;
		// content
		$template_content .= '<ul class="icons">';
		$template_content .= createListItem(DBI_DATABASE_OPTIMIZE,'zo_database_maintenance.php?task=optimize','dbStatus');
		$template_content .= createListItem(GGA_COMPLETE_MISSING_SOURCES,'zo_database_maintenance.php?task=completeWordSources','dbStatus');
		$template_content .= '</ul>';
		$template_content .= createBackLink (DBI_DATABASE,'zo_database.php');
	break;

}

// sidebar
$template_sidebar = '';

// call template
require_once 'templates/ini.php';

?>
