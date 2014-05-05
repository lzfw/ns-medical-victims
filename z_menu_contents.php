<?php
// cms content administration
// last known update: 2014-01-27

require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('edit');

$layout
	->set('title',Z_CONTENTS)
	->set('content',
		'<ul class="icons">'.
		($dbi->checkUserPermission('system')
			? createListItem(Z_TEXTBLOCKS,'z_list_textblocks','textblocks')
			: NULL
		).
		'</ul>'.
		createHomeLink ())
	->set('sidebar',$dbi->getTextblock_HTML ('z_contents'))
	->cast();

?>