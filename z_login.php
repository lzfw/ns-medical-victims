<?php
// CMS file: authentication
// last known update: 2014-02-03

require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$form = new Form ('user');

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name);

$form
	->setLabel(Z_LOGIN_PROMPT);

$form->addField ('name',TEXT,30,REQUIRED)			->setLabel (Z_USER_NAME);
$form->addField ('password',PASSWORD,30,REQUIRED)	->setLabel (Z_USER_PASSWORD);

$form->addCondition (MYSQL_STATEMENT,"SELECT user_id FROM z_users WHERE name='{name}' AND password='{password}'",Z_INVALID_LOGIN);

$form
	->addButton (BACK)
	->addButton (SUBMIT);

$form
	->addAction (QUERY_TO_SESSION,"SELECT * FROM z_users WHERE name='{name}' AND password='{password}'",Z_SESSION_NAME)
	->addAction (REDIRECT);

$layout
	->set('title',Z_LOGIN)
	->set('content',$form->run ())
	->cast();

?>