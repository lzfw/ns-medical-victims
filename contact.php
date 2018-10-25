<?php
// content: contact page (public)
// last known update: 2014-01-27

require_once 'zefiro/ini.php';

$dbi->setUserVar ('view',getUrlParameter('view'),'default');

$layout
	->set('title',L_SITE_CONTACT)
	->set('content',
		$dbi->getTextblock_HTML ('contact')
		.createHomeLink()
	)
	->cast();

?>