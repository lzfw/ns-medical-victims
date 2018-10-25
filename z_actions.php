<?php
require_once 'zefiro/ini.php';

if (
	(isset($_SERVER['HTTP_REFERER']))
	&& (
		stripos($_SERVER['HTTP_REFERER'],'https://'.$_SERVER['SERVER_NAME']) === 0
		|| 
		stripos($_SERVER['HTTP_REFERER'],'https://www.'.$_SERVER['SERVER_NAME']) === 0
		)
	) {
	$redirect_url = $_SERVER['HTTP_REFERER'];
}
else {
	$redirect_url = 'index';
}

switch ($_GET['action']) {

	// LANGUAGE SETTING
	case 'setLanguage':
		if (isset($_GET['language']) && isset($GLOBALS['z_languages'][$_GET['language']])) {
			$_SESSION[Z_SESSION_NAME]['language'] = $_GET['language'];
		}
		break;
	
	// WHEN COMMAND IS NOT ENLISTED, DO NOTHING
	default:
		break;

}

header('Location: '.$redirect_url);

?>