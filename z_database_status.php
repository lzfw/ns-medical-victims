<?php
// CMS file: database management
// last known update: 2013-01-22

require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('system');

// title
$template_title = DBI_DATABASE_STATUS;

// breadcrumbs
$dbi->addBreadcrumb (DBI_ADMIN,'zo_admin.php');
$dbi->addBreadcrumb (DBI_DATABASE,'zo_database.php');
$dbi->addBreadcrumb (DBI_DATABASE_STATUS);

// status query
$querystring = "
	SELECT 'sources' AS description, COUNT(s.source_id) AS total FROM sources s
	UNION (SELECT 'filecards' AS description, COUNT(f.filecard_id) AS total FROM filecards f)
	UNION (SELECT 'filecards-sources' AS description, COUNT(f.filecard_id) AS total FROM filecards f WHERE source_id=0)
	UNION (SELECT 'words' AS description, COUNT(g.word_id) AS total FROM glossary g)
	UNION (SELECT 'words-source' AS description, COUNT(g.word_id) AS total FROM glossary g WHERE g.source_id=0)
	UNION (SELECT 'words-filecard' AS description, COUNT(g.word_id) AS total FROM glossary g WHERE g.filecard_id=0)
";

$query = $dbi->connection->query($querystring);
if (!$query) {
    return DBI_ERROR_ABORTED;
}
$totals = array();
while ($fetch = $query->fetch_object()) {
	$totals[] = number_format($fetch->total,0,DBI_DECIMAL_SEPARATOR,DBI_THOUSANDS_SEPARATOR);
}

// content
$template_content = '';
$template_content .= '<table class="grid">';
$template_content .= '<tr><td>'.GGA_SOURCES.'</td><td class="align-right">'.$totals[0].'</td></tr>';
$template_content .= '<tr><td>'.GGA_FILECARDS.'</td><td class="align-right">'.$totals[1].'</td></tr>';
$template_content .= '<tr><td>'.GGA_FILECARDS_WITHOUT_SOURCE.'</td><td class="align-right">'.$totals[2].'</td></tr>';
$template_content .= '<tr><td>'.GGA_WORDS.'</td><td class="align-right">'.$totals[3].'</td></tr>';
$template_content .= '<tr><td>'.GGA_WORDS_WITHOUT_SOURCE.'</td><td class="align-right">'.$totals[4].'</td></tr>';
$template_content .= '<tr><td>'.GGA_WORDS_WITHOUT_FILECARD.'</td><td class="align-right">'.$totals[5].'</td></tr>';
$template_content .= '</table>';

$template_content .= '<div class="buttons">';
$template_content .= createButton (DBI_OK,'zo_database.php','icon ok');
$template_content .= '</div>';

// sidebar
$template_sidebar = '';

// call template
require_once 'templates/ini.php';

?>
