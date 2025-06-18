<?php
// cms content administration
// last known update: 2014-01-27

require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('system');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');

$layout
	->set('title',L_DATABASE)
	->set('content',
		'<ul class="icons">'.
			createListItem(L_DATABASE_STATUS,'z_database_status','dbStatus').
			createListItem(L_DATABASE_MAINTENANCE,'z_database_maintenance','dbMaintenance').
			($dbi->checkUserPermission('system')
				? (
					createListItem(L_DATABASE_IMPORT,'z_database_import','dbImport').
					createListItem(L_DATABASE_BACKUP,'z_database_backup','dbBackup').
					createListItem(L_DATABASE_RECOVERY,'z_database_recovery','dbRecovery')
				) : NULL
			).
		'</ul>'.
		createBackLink (L_ADMIN,'z_menu_admin')
	)
	->set('sidebar',$dbi->getTextblock_HTML ('z_database'))
	->cast();
