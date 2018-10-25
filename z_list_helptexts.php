<?php
// CMS file: textblock management
// last known update: 2013-01-22

require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('system');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin.php');

// query
$textblocks_querystring = "
	SELECT t.*, uc.display_name AS user_created, um.display_name AS user_modified
	FROM z_textblocks t
	LEFT OUTER JOIN z_users uc
	ON uc.user_id = t.created_user_id
	LEFT OUTER JOIN z_users um
	ON um.user_id = t.modified_user_id
	WHERE permission = 'system'
	ORDER BY name
";
$textblocks_query = $dbi->connection->query($textblocks_querystring);

// content
$content = '';
$content .= '<table class="grid">'.PHP_EOL;
$content .= '<tr><th>'.L_HELPTEXT_NAME.'</th>';
$content .= '<th>'.L_OPTIONS.'</th>';
$content .= '</tr>'.PHP_EOL;
while ($textblock = $textblocks_query->fetch_object()) {
	$content .= '<tr>'.PHP_EOL;
	$content .= "<td>$textblock->name</td>";
	$content .= '<td class="nowrap">'.
		createSmallButton(L_EDIT,'z_edit_helptext.php?textblock_id='.$textblock->textblock_id,'icon edit').
		createSmallButton(L_REMOVE,'z_remove_helptext.php?textblock_id='.$textblock->textblock_id,'icon remove').
		'</td>';
	$content .= '</tr>'.PHP_EOL;
}
$content .= '</table>'.PHP_EOL;
$content .= '<p class="buttons">';
$content .= createButton (L_ADD_HELPTEXT,'z_edit_helptext.php','icon addHelptext');
$content .= '</p>'.PHP_EOL;
$content .= createBackLink (L_ADMIN,'z_admin.php');

$layout
	->set('title',L_HELPTEXTS)
	->set('content',$content)
	->cast();
