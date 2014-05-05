<?php
// zefiro authentication (public)
// last known update: 2014-01-27

require_once 'zefiro/ini.php';

$layout
	->set('title',Z_PERMISSION_REQUIRED)
	->set('content',
		'<p>'.Z_PERMISSIONS_INSUFFICIENT.'</p>'.
		($dbi->checkUserAuthentication() ?
			(
				'<p>'.Z_RELOGIN_OR_GO_HOME.'</p>'.
				'<p class="buttons">'.
				createBackButton (Z_BACK).
				createButton (Z_HOME,'index','icon home').
				createButton (Z_LOGIN,'z_login','icon login').
				'</p>'
			) : (
				'<p>'.Z_LOGIN_OR_GO_HOME.'</p>'.
				'<p class="buttons">'.
				createBackButton (Z_BACK).
				createButton (Z_HOME,'index','icon home').
				createButton (Z_LOGIN,'z_login','icon login').
				'</p>'
			)
		)
	)
	->cast();

?>