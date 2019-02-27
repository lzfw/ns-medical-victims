<?php
// ZEFIRO INITIALIZATION
// last known update: 2014-02-03

// configure session
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 1);
ini_set('session.gc_maxlifetime', 900); // Session-Dauer in Sekunden
session_start();

// load configuration
// customization:
// - delete custom/config.php
// - make a copy of custom/config.default.php
// - rename this file to custom/config.php
if(file_exists('custom/config.php')) {
    require_once 'custom/config.php';
} else {
    require_once 'custom/config.default.php';
    //require_once 'zefiro/config.default.php';
}

// set default language, if neccessary
if (!isset ($_SESSION[Z_SESSION_NAME]['language'])) {
	$_SESSION[Z_SESSION_NAME]['language'] = Z_DEFAULT_LANGUAGE;
}
define('USER_LANGUAGE',$_SESSION[Z_SESSION_NAME]['language']);

// set timezone
date_default_timezone_set(Z_DEFAULT_TIMEZONE);

// load language files
require_once 'zefiro/translations/dictionary.'.USER_LANGUAGE.'.php';
require_once 'custom/translations/dictionary.'.USER_LANGUAGE.'.php';

// import libraries
require_once 'zefiro/lib/zefiro.php';
require_once 'zefiro/lib/buttons.php';
require_once 'zefiro/lib/dbi.php';
require_once 'zefiro/lib/layout.php';
require_once 'zefiro/lib/html.php';
require_once 'zefiro/lib/url.php';

// import special libraries
// uncomment if not required
require_once 'zefiro/lib/translit.php';

$dbi = new DBI ($db_host,$db_user,$db_pass,$db_name);

// maintenance mode
// uncomment when updating the database
//$dbi->maintenance = true;

// load layout definition
require_once 'custom/layout/definition.php';

// bookmarks
if (!isset($_SESSION[Z_SESSION_NAME]['bookmarks'])) {
	$_SESSION[Z_SESSION_NAME]['bookmarks'] = array();
}
$zefiroBookmarks =& $_SESSION[Z_SESSION_NAME]['bookmarks'];

// uncomment for session testing
//echo '<pre>'; print_r($_SESSION); echo '</pre>';

?>