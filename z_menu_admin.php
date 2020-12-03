<?php
// cms administration
// last known update: 2014-01-27

require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('admin');

$layout
	->set('title',L_ADMIN)
	->set('content',
		'<ul class="icons">'.
		createListItem(L_DATABASE,'z_database','db').
		createListItem(L_USER_ACCOUNTS,'z_list_users','users').
		createListItem(L_REMOTE_ACCESSES,'z_list_remote','remotes').
		createListItem(L_VIEW_LOG,'z_log','clipboard-list').
		($dbi->checkUserPermission('system')
			? createListItem(L_HELPTEXTS,'z_list_helptexts','helptexts')
			: NULL
		).
		'</ul>'
		.createHomeLink()
	)
	->set('sidebar',$dbi->getTextblock_HTML ('z_admin'))
	->cast();

?>
