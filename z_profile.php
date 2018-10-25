<?php
// CMS file: user profile
// last known update: 2014-02-03

require_once 'zefiro/ini.php';

$dbi->setUserVar ('user_id', getUrlParameter('user_id'), 0);
$dbi->setUserVar ('user_name', getUrlParameter('user_name'), 0);
$dbi->setUserVar ('view',getUrlParameter('view'),'default');

$user_query = array();
if ($dbi->getUserVar('user_id')) $user_query[] = 'user_id='.$dbi->getUserVar('user_id');
if ($dbi->getUserVar('user_name')) $user_query[] = 'user_name='.$dbi->getUserVar('user_name');
$dbi->setUserVar('querystring', implode('&',$user_query));

$title = '';
$content = '';

if ($dbi->getUserVar('user_id')) {
	// SHOW USER BY ID
	$user_querystring = "
		SELECT u.*
		FROM z_users u
		WHERE u.user_id = {$dbi->user['user_id']}
	";
	$user_query = $dbi->connection->query($user_querystring);
	$user = $user_query->fetch_object();
	$title .= $user->display_name;
	$content .= SimpleMarkup_HTML($user->profile);
}
elseif ($dbi->getUserVar('user_name')) {
	// SHOW USER BY NAME
	$user_querystring = "
		SELECT u.*
		FROM z_users u
		WHERE u.name = '{$dbi->user['user_name']}'
	";
	$user_query = $dbi->connection->query($user_querystring);
	$user = $user_query->fetch_object();
	$title .= $user->display_name;
	$content .= SimpleMarkup_HTML($user->profile);
}
else {
	// LIST OF USERS
	$users_querystring = "
		SELECT u.*
		FROM z_users u
		ORDER BY u.order_name, u.display_name
	";
	$users_query = $dbi->connection->query($users_querystring);
	$title .= L_USERS;
	$content .= '<ul>'.PHP_EOL;
	while ($user = $users_query->fetch_object()) {
		$content .= '<li>';
		$content .= '<a href="z_profile.php?user_id='.$user->user_id.'">'.$user->display_name.'</a>';
		$content .= '</li>'.PHP_EOL;
	}
	$content .= '</ul>'.PHP_EOL;
}

$layout
	->set('title',$title)
	->set('content',
		$content.
		createHomeLink()
	)
	->cast();

?>
