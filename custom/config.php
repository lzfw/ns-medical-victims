<?php
// Zefiro Configuration File
// last update: 2014-01-27

// PHP DEBUGGING INFO ==========================================================

// uncomment this line in productive environment
//ini_set('display_errors', 0);
//error_reporting(0);

// uncomment this line in development environment
ini_set('display_errors', 1);
error_reporting(E_ALL);

// LOCALIZATION ================================================================

$zfLanguageList = array(
	'de' => 'Deutsch',
	'en' => 'English'
);

// language parameters
define ('Z_DEFAULT_LANGUAGE',	'de');

// SESSION =====================================================================

// references
define ('Z_SESSION_NAME',		'zefiro');
define ('Z_LANGUAGE_VAR',		'language');
define ('Z_BOOKMARKS_ARRAY',	'bookmarks');
define ('Z_USER_ARRAY',			'user');


// SERVER/DATABASE CONFIGURATION ===============================================

switch ($_SERVER['SERVER_NAME']) {
	case 'localhost': {
		$db_host = 'host';
		$db_user = 'user';
		$db_pass = 'pass';
		$db_name = 'name';
		break;
	}
	default: {
		echo 'Sorry. The server name is not enlisted in the DBI configuration: '.$_SERVER['SERVER_NAME'];
		die;
	}
}

// DATABASE INTERFACE ==========================================================

// language
define('DBI_LANGUAGE',					'de');

// webmaster / reporter addresses
define('DBI_WEBMASTER_MAIL',			'webmaster@example.com');
define('DBI_WEBMASTER_NAME',			'Webmaster');
define('DBI_REPORTER_MAIL',				'bot@example.com');
define('DBI_REPORTER_NAME',				'Report Bot');

// symbols
define('DBI_BREADCRUMB_SYMBOL',			'<span class="pointer" dir="ltr">&#x25ba;</span>'.PHP_EOL);
define('DBI_SEPARATOR_SYMBOL',			'<span class="separator" dir="ltr">|</span>'.PHP_EOL);
define('DBI_UP_SYMBOL',					'<span class="pointer" dir="ltr">&#x25b2;</span>');
define('DBI_DOWN_SYMBOL',				'<span class="pointer" dir="ltr">&#x25bc;</span>');
define('DBI_FIRST_SYMBOL',				'<span class="pointer" dir="ltr">&#x25c4;</span> ');
define('DBI_SKIP_BACK_SYMBOL',			'<span class="pointer" dir="ltr">&#x25c4;&#x25c4;</span> ');
define('DBI_PREVIOUS_SYMBOL',			'<span class="pointer" dir="ltr">&#x25c4;</span> ');
define('DBI_NEXT_SYMBOL',				'<span class="pointer" dir="ltr">&#x25ba;</span> ');
define('DBI_SKIP_FORWARD_SYMBOL',		'<span class="pointer" dir="ltr">&#x25ba;&#x25ba;</span> ');
define('DBI_LAST_SYMBOL',				'<span class="pointer" dir="ltr">&#x25ba;</span> ');

// lists
define('DBI_LIST_ROWS_MAX',				5000);
define('DBI_LIST_ROWS_PAGE',			20);
define('DBI_LIST_ROWS_SKIP',			200); // 0 = disabled

?>