<?php
// ZEFIRO INITIALIZATION
// last known update: 2014-02-03

// configure session
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 1);
ini_set('session.gc_maxlifetime', 3600); // Session-Dauer in Sekunden
ini_set('session.use_strict_mode', 1);
ini_set('session.use_only_cookies', 1);
// session cookie: not readable by scripts, not sent with cross-site requests, HTTPS only behind TLS
session_set_cookie_params(array(
	'lifetime' => 0,
	'path' => '/',
	'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
		|| (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'),
	'httponly' => true,
	'samesite' => 'Lax'
));
session_start();

// security headers: no framing by other sites, no MIME sniffing, no URLs leaked to other sites
header('X-Frame-Options: SAMEORIGIN');
header("Content-Security-Policy: frame-ancestors 'self'; object-src 'none'; base-uri 'self'; form-action 'self'");
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');

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
require_once 'zefiro/lib/dbi_log_entry.php';
require_once 'zefiro/lib/layout.php';
require_once 'zefiro/lib/html.php';
require_once 'zefiro/lib/url.php';

// CSRF protection: every POST and every confirmed removal (confirm=yes) needs the session token
if (!isset($_SESSION[Z_SESSION_NAME]['csrf_token'])) {
	$_SESSION[Z_SESSION_NAME]['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_sent = ($_SERVER['REQUEST_METHOD'] === 'POST') ? ($_POST['csrf-token'] ?? '') : ($_GET['csrf-token'] ?? '');
if (($_SERVER['REQUEST_METHOD'] === 'POST' || (isset($_GET['confirm']) && $_GET['confirm'] === 'yes'))
		&& !(is_string($csrf_sent) && hash_equals(csrfToken(), $csrf_sent))) {
	http_response_code(403);
	echo L_FORM_EXPIRED;
	exit;
}
unset($csrf_sent);

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