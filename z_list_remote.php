<?php
// CMS file: remote management
// last known update: 2013-01-22

require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('system');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin.php');

// query
$remote_querystring = "SELECT * FROM z_users WHERE remote <> '' ORDER BY name";
$remote_query = $dbi->connection->query($remote_querystring);

// content
$content = '';
$content .= '<table class="grid">';
$content .= '<tr><th>'.L_USER_DISPLAY_NAME.'</th><th>'.L_USER_NAME.'</th><th>'.L_IP_ADDRESS.'</th><th>'.L_USER_GROUP.'</th><th>'.L_USER_PERMISSIONS.'</th>';
if ($dbi->checkUserPermission('system')) {
	$content .= '<th>'.L_OPTIONS.'</th>';
}
$content .= '</tr>';
while ($remote = $remote_query->fetch_object()) {
	$content .= '<tr>';
	$content .= "<td>$remote->display_name</td>";
	$content .= "<td>$remote->name</td>";
	$content .= "<td>$remote->remote</td>";
	$content .= "<td>$remote->group</td>";
	$content .= "<td>$remote->permissions</td>";
	if ($dbi->checkUserPermission('system')) {
		$content .= '<td class="nowrap">'.
			createSmallButton(L_EDIT,'z_edit_remote.php?user_id='.$remote->user_id,'icon edit').
			createSmallButton(L_DELETE,'z_remove_remote.php?user_id='.$remote->user_id,'icon delete').
			"</td>";
	}
	$content .= '</tr>';
}
$content .= '<tr><td>('.L_CURRENT.')</td><td>&ndash;</td><td>'.$_SERVER['REMOTE_ADDR'].'</td><td>&ndash;</td><td>&ndash;</td><td>&ndash;</td></tr>';
$content .= '</table>';
if ($dbi->checkUserPermission('system')) {
	$content .= '<div class="buttons">';
	$content .= createButton (L_NEW_REMOTE_ACCESS,'z_edit_remote.php','icon addRemote');
	$content .= '</div>';
}
$content .= createBackLink (L_ADMIN,'z_menu_admin.php');

$layout
	->set('title',L_REMOTE_ACCESS)
	->set('content',$content)
	->cast();
