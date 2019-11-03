<?php
// CMS file: authentication
// last known update: 2014-02-03

require_once 'zefiro/ini.php';

function log_logout($uid, $name) {
    $details = 'NAME: ' .     $name .
        '; REMOTE: ' .        $_SERVER['HTTP_X_FORWARDED_FOR'] .
        '; FORWARDED_FOR: ' . $_SERVER['HTTP_X_FORWARDED_FOR'];

    $log_entry = new DBI_Log_Entry(
        'LOGOUT',
        'z_users',
        TRUE,
        $uid,
        $details
    );
    $log_entry->store();
}

$dbi->requireUserAuthentication ();

$name = $_SESSION[Z_SESSION_NAME]['user']['name'];
$uid = $_SESSION[Z_SESSION_NAME]['user']['user_id'];

unset($_SESSION[Z_SESSION_NAME]['user']);

$dbi->importUserData();

log_logout($uid, $name);

$layout
	->set('title',L_LOGOUT)
	->set('content',
		'<p>'.L_LOGGED_OUT.'</p>'.
		createHomeLink ()
	)
	->cast();

?>