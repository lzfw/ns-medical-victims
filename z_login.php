<?php
// CMS file: authentication
// last known update: 2014-02-03

require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

function update_user_pass($user_name, $pass, $link) {
	// re-hash password with stronger crypto algorithm
	// and store it to db
	$hash = password_hash($pass, PASSWORD_DEFAULT);
	$stmt = $link->stmt_init();
	$stmt->prepare(
			"UPDATE z_users
			SET password = ?
			WHERE name = ?");
	$stmt->bind_param('ss', $hash, $user_name);
	$stmt->execute();
	$stmt->close();
}

$form = new Form ('user');

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name);

$form
	->setLabel(L_LOGIN_PROMPT);

$form->addField ('name',TEXT,30,REQUIRED)			->setLabel (L_USER_NAME);
$form->addField ('password',PASSWORD,30,REQUIRED)	->setLabel (L_USER_PASSWORD);

$form->addCondition (USER_FUNCTION, function() use ($form) {
	$password_matches = false;
	$stored_name = '';
	$stored_password = '';
	$name = $form->Fields['name']->user_value;
	$pass = $form->Fields['password']->user_value;
	$stmt = $form->Connection->link->stmt_init();
	$stmt->prepare(
			"SELECT name, password
			FROM z_users
			WHERE name=?");
	$stmt->bind_param('s', $name);
	$stmt->execute();
	$stmt->bind_result($stored_name, $stored_password_hash);
	$stmt->fetch();
	$stmt->close();
	if (preg_match('/^\$\S{1,6}\$/', $stored_password_hash)) {
		$password_matches = password_verify($pass, $stored_password_hash);
		if ($password_matches && password_needs_rehash($stored_password_hash, PASSWORD_DEFAULT)) {
			update_user_pass($name, $pass, $form->Connection->link);
		}
	} else {
		$password_matches = ($pass === $stored_password_hash) && ($stored_password_hash !== "");
		if ($password_matches) {
			update_user_pass($name, $pass, $form->Connection->link);
		}
	}
	return $password_matches;
}, L_INVALID_LOGIN);

$form
	->addButton (BACK)
	->addButton (SUBMIT);

$form
	->addAction (QUERY_TO_SESSION,"SELECT * FROM z_users WHERE name='{name}'",L_SESSION_NAME)
	->addAction (REDIRECT);

$layout
	->set('title',L_LOGIN)
	->set('content',$form->run ())
	->cast();

?>