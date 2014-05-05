<?php
// content: site notice, legal disclaimer, publishing information (public)
// last known update: 2014-01-27

require_once 'zefiro/ini.php';

$dbi->setUserVar ('view',getUrlParameter('view'),'default');

$layout
	->set('title',Z_SITE_NOTICE)
	->set('content',
		$dbi->getTextblock_HTML ('notice')
		.createHomeLink()
	)
	->cast();

?>