<?php
// CMS file: search results (public)
// last known update: 2013-01-22

require_once 'setup/ini.php';

if (implode('',$_GET) == '') {
	header("Location: search.php");
	exit;
}

// url parameters
$dbi->setUserVar ('view',getUrlParameter('view'),'default');

// sort options
$dbi->setUserVar ('sort',getUrlParameter('sort'),'my_item_1');
$dbi->setUserVar ('order',getUrlParameter('order'),'ASC');
$dbi->setUserVar ('skip',getUrlParameter('skip'),0);

// search
if (!empty($_GET)) {
	// GET-Parameter auslesen
	$exact_fields = array (
		'source_id',
		'my_item_1',
		'my_item_2'
	);
	$like_fields = array (
		'my_item_3','my_item_4'
	);
	$double_fields = array (
		'my_item_5','my_item_6'
	);

	// GET-String rekonstruieren
	// wichtig fuer Blaetterfunktion
	$query = array();
	foreach ($exact_fields as $field) {
		if (isset($_GET[$field]) && $_GET[$field] != '') $query[] = "$field={$_GET[$field]}";
	}
	foreach ($like_fields as $field) {
		if (isset($_GET[$field]) && $_GET[$field] != '') $query[] = "$field={$_GET[$field]}";
	}
	foreach ($double_fields as $field) {
		if (isset($_GET[$field]) && $_GET[$field] != '') $query[] = "$field={$_GET[$field]}";
	}
	$dbi->setUserVar('querystring',implode('&',$query));

	// Querystrings generieren
	$querystring_count = 'SELECT COUNT(g.word_id) AS total FROM glossary g';
	$querystring = 'SELECT g.*, s.name AS source_name FROM glossary g LEFT OUTER JOIN sources s USING (source_id)';
	$querystring_where = array();
	// MySQL-Zeichen rausfiltern, Trunkierungszeichen durch MySQL-Zeichen ersetzen
	$filter_chars = array("'", '%', '_', '*', '٭');
	$replace_chars = array('', ' ', ' ', '%', '%');
	foreach ($exact_fields as $field) {
		if (getUrlParameter($field)) $querystring_where[] = "g.$field = '".getUrlParameter($field)."'";
	}
	foreach ($like_fields as $field) {
		if (getUrlParameter($field)) {
			$filtered_field = str_replace($filter_chars, $replace_chars, getUrlParameter($field));
			$querystring_where[] = "g.$field LIKE '".$filtered_field."'";
		}
	}
	foreach ($double_fields as $field) {
		if (getUrlParameter($field)) {
			$filtered_field = str_replace($filter_chars, $replace_chars, getUrlParameter($field));
			$querystring_where[] = "(g.$field LIKE '".$filtered_field."' OR g.$field = '".getUrlParameter($field)."')";
		}
	}
	if (count($querystring_where)>0) {
		$querystring_where = ' WHERE '.implode(' AND ',$querystring_where);
	}
	$querystring_count .= $querystring_where;
	$querystring .= $querystring_where;
	// Gesamtanzahl der Suchergebnisse feststellen
	$total_results_query = $dbi->connection->query($querystring_count);
	$total_results = $total_results_query->fetch_object();
	$dbi->setUserVar('total_results',$total_results->total);
}

// suchstring fuer mysql-query
$querystring_orderby = " ORDER BY g.gr_lexeme='', g.ar_lexeme='', {$dbi->user['sort']} {$dbi->user['order']} LIMIT ".($dbi->user['skip']).','.Z_LIST_ROWS_PAGE;
$words_query = $dbi->connection->query($querystring.$querystring_orderby);

// anzahl der gefundenen titel ausgeben

// template variables
$template_title = '';
$template_content = '';
$template_sidebar = '';

// title
$template_title .= L_RESULTS;

// breadcrumbs
$dbi->addBreadcrumb (L_SEARCH,'search.php');
$dbi->addBreadcrumb (L_RESULTS);

// content
$template_content .= '<p><em>';
$suche_nach = array();
if (isset($_GET['my_id']) && $_GET['my_id']) $suche_nach[] = 'my_id = '.$_GET['source_id'];
if (isset($_GET['my_item']) && $_GET['my_item']) $suche_nach[] = 'my_item = '.$_GET['gr_lexeme'];
$template_content .= implode(', ',$suche_nach);
$template_content .= '</em></p>';

$template_content .= $dbi->getListView('gga_wordsbyquery',$words_query);
$template_content .= '<div class="buttons">';
$template_content .= createButton (L_MODIFY_SEARCH,'search.php?'.$dbi->getUserVar('querystring'),'icon search');
$template_content .= createButton (L_NEW_SEARCH,'search.php','icon search');
$template_content .= '</div>';

$template_sidebar = '<h3>'.L_HELP.'</h3>';
$template_sidebar .= $dbi->getHelptext_HTML ('results');

// call template
require_once 'templates/ini.php';

