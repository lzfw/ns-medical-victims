<?php
// Zefiro Configuration File

// PHP DEBUGGING INFO ==========================================================

// uncomment this line in productive environment
//ini_set('display_errors', 0);
//error_reporting(0);

// uncomment this line in development environment
ini_set('display_errors', 1);
error_reporting(E_ALL);

// LOCALIZATION ================================================================

$z_languages = array(
	'de' => 'Deutsch',
	'en' => 'English'
	// add more languages to choose from, requires translation files!
);

define ('Z_DEFAULT_LANGUAGE', 'en');
define ('Z_DEFAULT_TIMEZONE', 'Europe/Berlin');

// SESSION =====================================================================

// references
define ('Z_SESSION_NAME',		'zefiro');
define ('Z_LANGUAGE_VAR',		'language');
define ('Z_BOOKMARKS_ARRAY',	'bookmarks');
define ('Z_USER_ARRAY',			'user');


// SERVER/DATABASE CONFIGURATION =============================================

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
define('Z_LANGUAGE',				'de');

// webmaster / reporter addresses
define('Z_WEBMASTER_MAIL',			'webmaster@example.com');
define('Z_WEBMASTER_NAME',			'Webmaster');
define('Z_REPORTER_MAIL',			'bot@example.com');
define('Z_REPORTER_NAME',			'Report Bot');

// symbols
define('Z_BREADCRUMB_SYMBOL',		'<span class="pointer" dir="ltr">&#x25ba;</span>'.PHP_EOL);
define('Z_SEPARATOR_SYMBOL',		'<span class="separator" dir="ltr">|</span>'.PHP_EOL);
define('Z_UP_SYMBOL',				'<span class="pointer" dir="ltr">&#x25b2;</span>');
define('Z_DOWN_SYMBOL',				'<span class="pointer" dir="ltr">&#x25bc;</span>');
define('Z_FIRST_SYMBOL',			'<span class="pointer" dir="ltr">&#x25c4;</span> ');
define('Z_SKIP_BACK_SYMBOL',		'<span class="pointer" dir="ltr">&#x25c4;&#x25c4;</span> ');
define('Z_PREVIOUS_SYMBOL',			'<span class="pointer" dir="ltr">&#x25c4;</span> ');
define('Z_NEXT_SYMBOL',				'<span class="pointer" dir="ltr">&#x25ba;</span> ');
define('Z_SKIP_FORWARD_SYMBOL',		'<span class="pointer" dir="ltr">&#x25ba;&#x25ba;</span> ');
define('Z_LAST_SYMBOL',				'<span class="pointer" dir="ltr">&#x25ba;</span> ');

// lists
define('Z_LIST_ROWS_MAX',			5000);
define('Z_LIST_ROWS_PAGE',			20);
define('Z_LIST_ROWS_SKIP',		200); // 0 = disabled
define('Z_LIST_ROWS_PAGE',		50);
define('Z_LIST_ROWS_SKIP',		Z_LIST_ROWS_PAGE * 10); // 0 = disabled
