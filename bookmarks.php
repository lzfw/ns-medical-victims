<?php
// CMS file: bookmarks (public)
// last known update: 2013-01-22

require_once 'setup/ini.php';

// url parameters
$dbi->setUserVar ('action',getUrlParameter('action'),NULL);
$dbi->setUserVar ('word_id',getUrlParameter('id'),0);
$dbi->setUserVar ('return',getUrlParameter('return'),NULL);
$dbi->setUserVar ('view',getUrlParameter('view'),'default');

// sort options
$dbi->setUserVar ('sort',getUrlParameter('sort'),'gr_lexeme');
$dbi->setUserVar ('order',getUrlParameter('order'),'ASC');
$dbi->setUserVar ('skip',getUrlParameter('skip'),0);

// url querystring
$user_query = array();
$dbi->setUserVar('querystring',implode('&',$user_query));

// template variables
$template_title = '';
$template_content = '';
$template_sidebar = '';

// title
$template_title .= DBI_BOOKMARKS;

// breadcrumbs
$dbi->addBreadcrumb (DBI_BOOKMARKS,'bookmarks.php');

// user action
switch ($dbi->getUserVar('action')) {

	case 'add':
		// ADD WORD TO BOOKMARK
		$word_querystring = 'SELECT g.* FROM glossary g WHERE g.word_id='.$dbi->getUserVar('word_id');
		$word_query = $dbi->connection->query($word_querystring);
		if ($word = $word_query->fetch_object($word_query)) {
			if (array_search($word->word_id,$_SESSION[BOOKMARKS]) === false) {
				$_SESSION[BOOKMARKS][] = $word->word_id;
			}
		}
	break;

	case 'remove':
	    // FIXME: Code is almost identical to lines above.
	    // This usually indicates unsufficient structuring.
	    // Here, it would be probably the best to have a clear separation
	    // between (data)model, view and controller
		// REMOVE WORD TO BOOKMARK
		$word_querystring = 'SELECT g.* FROM glossary g WHERE g.word_id='.$dbi->getUserVar('word_id');
		$word_query = $dbi->connection->query($word_querystring);
		if ($word = $word_query->fetch_object($word_query)) {
			if (($bookmark_id = array_search($word->word_id,$_SESSION[BOOKMARKS])) !== false) {
				unset ($_SESSION[BOOKMARKS][$bookmark_id]);
			}
		}
	break;

	case 'dump':
		$dbi->addBreadcrumb (DBI_DUMP_BOOKMARKS);
		// REMOVE ALL WORDS FROM BOOKMARKS
		$_SESSION[BOOKMARKS] = array();
		$template_content .= '<p>'.DBI_BOOKMARKS_DUMPED.'</p>';
	break;

	default:
		// VIEW BOOKMARKS
		if (count($_SESSION[BOOKMARKS]) > 0) {
			$bookmarks = implode(',',$_SESSION[BOOKMARKS]);
			// QUERY
			$bookmarks_querystring = "
				SELECT g.*, s.name AS source_name
				FROM glossary g
				LEFT OUTER JOIN sources s USING (source_id)
				LEFT OUTER JOIN filecards f USING (filecard_id)
				WHERE word_id IN ($bookmarks)
				ORDER BY {$dbi->getUserVar('sort')} {$dbi->getUserVar('order')}
				LIMIT {$dbi->getUserVar('skip')},".DBI_LIST_ROWS_PAGE;
			$bookmarks_query = $dbi->connection->query($bookmarks_querystring);
			$dbi->setUserVar('total_results',count($_SESSION[BOOKMARKS]));
			// LIST
			$template_content .= $dbi->getListView ('gga_wordsbyquery',$bookmarks_query);
			// OPTIONS
			$dbi->addOption (DBI_SEND_BOOKMARKS,'bookmarks_sendmail.php','icon sendBookmarks');
			$dbi->addOption (DBI_DUMP_BOOKMARKS,"?action=dump",'icon dumpBookmarks');
		}
		else {
			$template_content .= '<p>'.DBI_BOOKMARKS_EMPTY.'</p>'.PHP_EOL;
		}
		$template_content .= createHomeLink();
	break;

}

// return to previous page?
if ($dbi->getUserVar('return') == 'yes') {
	header('Location: '.$_SERVER[HTTP_REFERER]);
}

// sidebar
$template_sidebar .= $dbi->getRubrics_HTML ();
$template_sidebar .= '<h3>'.DBI_HELP.'</h3>';
$template_sidebar .= $dbi->getHelptext_HTML ('bookmarks');
$template_sidebar .= $dbi->getReferences_HTML ();

// call template
require_once 'templates/ini.php';

