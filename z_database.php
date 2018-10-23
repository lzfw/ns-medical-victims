<?php
// cms content administration
// last known update: 2014-01-27

require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (Z_ADMIN,'z_menu_admin');

$layout
	->set('title',Z_DATABASE)
	->set('content',
		'<ul class="icons">'.
			createListItem(Z_DATABASE_STATUS,'z_database_status','dbStatus').
			createListItem(Z_DATABASE_MAINTENANCE,'z_database_maintenance','dbMaintenance').
			($dbi->checkUserPermission('system')
				? (
					createListItem(Z_DATABASE_IMPORT,'z_database_import','dbImport').
					createListItem(Z_DATABASE_BACKUP,'z_database_backup','dbBackup').
					createListItem(Z_DATABASE_RECOVERY,'z_database_recovery','dbRecovery')
				) : NULL
			).
		'</ul>'.
		createBackLink (Z_ADMIN,'z_menu_admin')
	)
	->set('sidebar',$dbi->getTextblock_HTML ('z_database'))
	->cast();
