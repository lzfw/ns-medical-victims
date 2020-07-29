<?php
// cms content administration
// adoptable to user tables
// last known update: 2014-01-27

require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$layout
	->set('title',L_CONTENTS)
	->set('content',
		'<ul class="icons">'.
		($dbi->checkUserPermission('system')
			? createListItem(L_TEXTBLOCKS,'z_list_textblocks','textblocks')
			: NULL
		).
		($dbi->checkUserPermission('view')
			? createListItem('Victims','nmv_list_victims','')
				//complete db d
				.($dbi->checkUserPermission('mpg')
						? NULL
						: createListItem('Biomedical Research','nmv_list_experiments','')
					)
			  . createListItem('Perpetrators','nmv_list_perpetrators','')
			  . createListItem('Institutions','nmv_list_institutions','')
			  . createListItem('Literature','nmv_list_literature','')
			  . createListItem('Sources','nmv_list_sources','')
			: NULL
		).
		'</ul>'.
		createHomeLink ())
	->set('sidebar',$dbi->getTextblock_HTML ('z_contents'))
	->cast();
