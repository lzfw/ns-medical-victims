<?php
// home page (public)
// last known update: 2014-01-27

require_once 'zefiro/ini.php';

$dbi->setUserVar ('view',getUrlParameter('view'),'default');

$layout
	->set('title',Z_HOME)
	->set('content',$dbi->getTextblock_HTML ('home'))
	->cast();

?>