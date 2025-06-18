<?php

require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');

// query: get user data
$user_querystring = "SELECT * FROM users WHERE 1 ORDER BY users.id";
$user_query = $dbi->connection->query($user_querystring);

$content = '';
$content = '<p>When a user of the website requests researchers access via the form on the website, <br>
                            an account with the data from the online-form is created automatically but not activated yet. <br>
                            If the "Board for Ethical Use and Access" gives permission, the account needs to be activated here.
            </p>';

$content .= '<br>' . createButton ('New Website User Account','website_user_edit','icon addUser') . '<br><br>';
$content .= '<table class="grid">';
$content .=
    '<tr>
        <th>' . 'ID' . '</th>
        <th>' . 'User Name'. '</th>
        <th>' . 'Email' . '</th>
        <th>' . 'Created at' . '</th>
        <th>' . 'Updated at' . '</th>
        <th>'. 'Role' .'</th>
        <th>'. 'Access pending?' .'</th>
        <th>'. 'Access granted?' .'</th>
        <th>'. 'Access expires' .'</th>
        <th>'. 'Must change password?' .'</th>
        <th>'. 'Options' .'</th>
    </tr>';

while ($user = $user_query->fetch_object()) {
	$content .= '<tr>';
	$content .= "<td> $user->id </td>";
	$content .= "<td> $user->name </td>";
	$content .= "<td> $user->email </td>";
	$content .= "<td> $user->created_at </td>";
	$content .= "<td> $user->updated_at </td>";
	$content .= "<td> $user->role </td>";
    $content .= "<td>" . ($user->access_pending == 1 ? 'yes' : '-') . "</td>";
    $content .= "<td>" . ($user->access_granted == 1 ? 'yes' : '-') . "</td>";
	$content .= "<td>" . ($user->access_expires_at ?? '-') . "</td>";
    $content .= "<td>" . ($user->must_change_password == 1 ? 'yes' : '-') . "</td>";
    if ($dbi->checkUserPermission('admin')) {
		$content .= '<td class="nowrap">'.
			createSmallButton('ACTIVATE ACCOUNT','website_user_activate?id='.$user->id,'icon edit').
			createSmallButton(L_EDIT,'website_user_edit?id='.$user->id,'icon edit').
			createSmallButton(L_DELETE,'website_user_remove?id='.$user->id,'icon delete').
			"</td>";
	}
	$content .= '</tr>';
}
$content .= '</table>';
if ($dbi->checkUserPermission('admin')) {
	$content .= '<div class="buttons">';
	$content .= createButton ('New Website User Account','website_user_edit','icon addUser');
	$content .= '</div>';
}
$content .= createBackLink (L_ADMIN,'z_menu_admin');

$layout
	->set('title', 'Website User Accounts')
	->set('content',$content)
	->cast();

?>