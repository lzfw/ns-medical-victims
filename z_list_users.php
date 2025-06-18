<?php
// CMS file: user management
// last known update: 2014-01-27

require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('system');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');

// query: get user data
//$user_querystring = "SELECT * FROM z_users WHERE password <> '' ORDER BY `group`,`permissions` DESC,`order_name`";
$user_querystring = "SELECT * FROM z_users WHERE password <> '' ORDER BY `order_name`";
$user_query = $dbi->connection->query($user_querystring);

$content = '';
$content = '<p class="red">Attention: these are users for the Interface, not the Website!<br>
If you grant access, the person might be able to change database contents.</p>';
$content .= '<table class="grid">';
$content .= '<tr><th>'.L_USER_DISPLAY_NAME.'</th><th>'.L_USER_NAME.'</th><th>'.L_USER_GROUP.'</th><th>'.L_USER_PERMISSIONS.'</th>';
if ($dbi->checkUserPermission('admin')) {
	$content .= '<th>'.L_OPTIONS.'</th>';
}
$content .= '</tr>';
while ($user = $user_query->fetch_object()) {
	$content .= '<tr>';
	$content .= '<td><a href="z_profile?user_id='.$user->user_id.'">'.$user->display_name.'</a></td>';
	$content .= "<td>$user->name</td>";
	$content .= "<td>$user->group</td>";
	$content .= "<td>$user->permissions</td>";
	if ($dbi->checkUserPermission('admin')) {
		$content .= '<td class="nowrap">'.
			createSmallButton(L_EDIT,'z_edit_user?user_id='.$user->user_id,'icon edit').
			createSmallButton(L_DELETE,'z_remove_user?user_id='.$user->user_id,'icon delete').
			"</td>";
	}
	$content .= '</tr>';
}
$content .= '</table>';
if ($dbi->checkUserPermission('admin')) {
	$content .= '<div class="buttons">';
	$content .= createButton (L_NEW_USER_ACCOUNT,'z_edit_user','icon addUser');
	$content .= '</div>';
}
$content .= createBackLink (L_ADMIN,'z_menu_admin');

$layout
	->set('title',L_USER_ACCOUNTS)
	->set('content',$content)
	->cast();

?>