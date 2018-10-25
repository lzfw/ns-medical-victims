<?php
// cms content administration
// last known update: 2014-01-27

require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('edit');

$layout
	->set('title',L_CONTENTS)
	->set('content',
		'<ul class="icons">'.
		($dbi->checkUserPermission('system')
			? createListItem(L_TEXTBLOCKS,'z_list_textblocks','textblocks')
			: NULL
		).
		'</ul>'.
		createHomeLink ())
	->set('sidebar',$dbi->getTextblock_HTML ('z_contents'))
	->cast();

?>