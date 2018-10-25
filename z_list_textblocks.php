<?php
// CMS file: textblock management
// last known update: 2013-01-22

require_once 'zefiro/ini.php';
$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents.php');

// query
$textblocks_querystring = "
	SELECT t.*, uc.display_name AS user_created, um.display_name AS user_modified
	FROM z_textblocks t
	LEFT OUTER JOIN z_users uc
	ON uc.user_id = t.created_user_id
	LEFT OUTER JOIN z_users um
	ON um.user_id = t.modified_user_id
	WHERE permission = 'admin'
	ORDER BY name
";
$textblocks_query = $dbi->connection->query($textblocks_querystring);

// content
$content = '';
$content .= '<table class="grid">'.PHP_EOL;
$content .= '<tr><th>'.L_TEXTBLOCK_NAME.'</th>';
$content .= '<th>'.L_OPTIONS.'</th>';
$content .= '</tr>'.PHP_EOL;
while ($textblock = $textblocks_query->fetch_object()) {
	$content .= '<tr>'.PHP_EOL;
	$content .= "<td>$textblock->name</td>";
	$content .= '<td class="nowrap">'.
		createSmallButton(L_EDIT,'z_edit_textblock.php?textblock_id='.$textblock->textblock_id,'icon edit').
		createSmallButton(L_REMOVE,'z_remove_textblock.php?textblock_id='.$textblock->textblock_id,'icon remove').
		'</td>';
	$content .= '</tr>'.PHP_EOL;
}
$content .= '</table>'.PHP_EOL;
$content .= '<p class="buttons">';
$content .= createButton (L_ADD_TEXTBLOCK,'z_edit_textblock.php','icon addTextblock');
$content .= '</p>'.PHP_EOL;
$content .= createBackLink (L_CONTENTS,'z_contents.php');

$layout
	->set('title',L_TEXTBLOCKS)
	->set('content',$content)
	->cast();

?>