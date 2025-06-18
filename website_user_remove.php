<?php
// last known update: 2014-02-04

require_once 'zefiro/ini.php';
require_once 'zefiro/lib/opendb_mysql.php';
require_once 'zefiro/lib/url.php';

$dbi->requireUserPermission ('admin');

// url parameters
$item_id = getUrlParameter('id',0);
$user_confirm = getUrlParameter('confirm',NULL);

// database connection
openDB ($db_host,$db_user,$db_pass,$db_name);

if ($item_query = $dbi->connection->query('SELECT * FROM users WHERE id='.$item_id)) {
	$item = $item_query->fetch_object();
	switch ($user_confirm) {

		case 'yes':
			// deletion confirmed
		    $dbi->connection->query('DELETE FROM users WHERE id='.$item_id);
			header('Location: website_user_list');
			break;

		case 'no':
			// deletion cancelled
			header('Location: website_user_list');
			break;

		default:
			break;
	}
}

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'website_user_list');

$layout
	->set('title',L_REMOVE_USER_ACCOUNT)
	->set('content',
		buildElement('p',L_REMOVE_USER_ACCOUNT.': "<em>'.$item->name.'</em>". '.L_ARE_YOU_SURE).
		buildElement('div','buttons',
			createButton (L_NO_CANCEL,'website_user_remove?id='.$item_id.'&confirm=no','icon no').
			createButton (L_YES_CONTINUE,'website_user_remove?id='.$item_id.'&confirm=yes','icon yes')
		)
	)
	->cast();

?>