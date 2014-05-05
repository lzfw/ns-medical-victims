<?php
// cms administration
// last known update: 2014-01-27

require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('admin');

$layout
	->set('title',Z_ADMIN)
	->set('content',
		'<ul class="icons">'.
		createListItem(Z_DATABASE,'z_database','db').
		createListItem(Z_USER_ACCOUNTS,'z_list_users','users').
		createListItem(Z_REMOTE_ACCESSES,'z_list_remote','remotes').
		($dbi->checkUserPermission('system')
			? createListItem(Z_HELPTEXTS,'z_list_helptexts','helptexts')
			: NULL
		).
		'</ul>'
		.createHomeLink()
	)
	->set('sidebar',$dbi->getTextblock_HTML ('z_admin'))
	->cast();

?>