<?php
// zefiro authentication (public)
// last known update: 2014-01-27

require_once 'zefiro/ini.php';

$layout
	->set('title',L_PERMISSION_REQUIRED)
	->set('content',
		'<p>'.L_PERMISSIONS_INSUFFICIENT.'</p>'.
		($dbi->checkUserAuthentication() ?
			(
				'<p>'.L_RELOGIN_OR_GO_HOME.'</p>'.
				'<p class="buttons">'.
				createBackButton (L_BACK).
				createButton (L_HOME,'index','icon home').
				createButton (L_LOGIN,'z_login','icon login').
				'</p>'
			) : (
				'<p>'.L_LOGIN_OR_GO_HOME.'</p>'.
				'<p class="buttons">'.
				createBackButton (L_BACK).
				createButton (L_HOME,'index','icon home').
				createButton (L_LOGIN,'z_login','icon login').
				'</p>'
			)
		)
	)
	->cast();

?>