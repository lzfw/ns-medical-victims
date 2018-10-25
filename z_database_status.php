<?php
// CMS file: database management
// last known update: 2013-01-22

require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('system');

// title
$template_title = L_DATABASE_STATUS;

// breadcrumbs
$dbi->addBreadcrumb (L_ADMIN,'z_admin.php');
$dbi->addBreadcrumb (L_DATABASE,'z_database.php');
$dbi->addBreadcrumb (L_DATABASE_STATUS);

// status query
$querystring = "
	SELECT 'my_item_1' AS description, COUNT(t.my_table_1_id) AS total FROM my_table_1 t
	UNION (SELECT 'my_item_2' AS description, COUNT(t.my_table_2_id) AS total FROM my_table_2 t)
	UNION (SELECT 'my_item_3' AS description, COUNT(t.my_table_3_id) AS total FROM my_table_3 t)
";

$query = $dbi->connection->query($querystring);
if (!$query) {
    return L_ERROR_ABORTED;
}
$totals = array();
while ($fetch = $query->fetch_object()) {
	$totals[] = number_format($fetch->total,0,L_DECIMAL_SEPARATOR,L_THOUSANDS_SEPARATOR);
}

// content
$template_content = '';
$template_content .= '<table class="grid">';
$template_content .= '<tr><td>my item 1</td><td class="align-right">'.$totals[0].'</td></tr>';
$template_content .= '<tr><td>my item 2</td><td class="align-right">'.$totals[1].'</td></tr>';
$template_content .= '<tr><td>my item 3</td><td class="align-right">'.$totals[2].'</td></tr>';
$template_content .= '</table>';

$template_content .= '<div class="buttons">';
$template_content .= createButton (L_OK,'z_database.php','icon ok');
$template_content .= '</div>';

// sidebar
$template_sidebar = '';

// call template
require_once 'templates/ini.php';

?>
