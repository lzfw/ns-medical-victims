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
$dbi->setUserVar ('sort',getUrlParameter('sort'),'gr_lexeme');
$dbi->setUserVar ('order',getUrlParameter('order'),'ASC');
$dbi->setUserVar ('skip',getUrlParameter('skip'),0);

// search
if (!empty($_GET)) {
	// GET-Parameter auslesen
	$exact_fields = array (
		'source_id',
		'gr_pos',
		'ar_pos',
		'ar_root_1','ar_root_2','ar_root_3','ar_root_4','ar_root_5','ar_stem',
	);
	$like_fields = array (
		'gr_lexeme','gr_expression','gr_reference'
	);
	$double_fields = array (
		'ar_lexeme','ar_expression','ar_reference'
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
	$total_results_query = mysql_query($querystring_count);
	$total_results = mysql_fetch_object($total_results_query);
	$dbi->setUserVar('total_results',$total_results->total);
}

// suchstring fuer mysql-query
$querystring_orderby = " ORDER BY g.gr_lexeme='', g.ar_lexeme='', {$dbi->user['sort']} {$dbi->user['order']} LIMIT ".($dbi->user['skip']).','.DBI_LIST_ROWS_PAGE;
$words_query = mysql_query($querystring.$querystring_orderby);

// anzahl der gefundenen titel ausgeben

// template variables
$template_title = '';
$template_content = '';
$template_sidebar = '';

// title
$template_title .= DBI_RESULTS;

// breadcrumbs
$dbi->addBreadcrumb (DBI_SEARCH,'search.php');
$dbi->addBreadcrumb (DBI_RESULTS);

// content
$template_content .= '<p><em>';
$suche_nach = array();
if (isset($_GET['source_id']) && $_GET['source_id']) $suche_nach[] = GGA_SOURCE.' = '.$_GET['source_id'];
if (isset($_GET['gr_lexeme']) && $_GET['gr_lexeme']) $suche_nach[] = GGA_GREEK_LEXEME.' = '.$_GET['gr_lexeme'];
if (isset($_GET['gr_pos']) && $_GET['gr_pos']) $suche_nach[] = GGA_GREEK_POS.' = '.$_GET['gr_pos'];
if (isset($_GET['gr_expression']) && $_GET['gr_expression']) $suche_nach[] = GGA_GREEK_EXPRESSION.' = '.$_GET['gr_expression'];
if (isset($_GET['gr_reference']) && $_GET['gr_reference']) $suche_nach[] = GGA_GREEK_REFERENCE.' = '.$_GET['gr_reference'];
if (isset($_GET['ar_lexeme']) && $_GET['ar_lexeme']) $suche_nach[] = GGA_ARABIC_LEXEME.' = '.$_GET['ar_lexeme'];
if (isset($_GET['ar_root_1']) && $_GET['ar_root_1']) $suche_nach[] = GGA_ARABIC_ROOT_1.' = '.$_GET['ar_root_1'];
if (isset($_GET['ar_root_2']) && $_GET['ar_root_2']) $suche_nach[] = GGA_ARABIC_ROOT_2.' = '.$_GET['ar_root_2'];
if (isset($_GET['ar_root_3']) && $_GET['ar_root_3']) $suche_nach[] = GGA_ARABIC_ROOT_3.' = '.$_GET['ar_root_3'];
if (isset($_GET['ar_root_4']) && $_GET['ar_root_4']) $suche_nach[] = GGA_ARABIC_ROOT_4.' = '.$_GET['ar_root_4'];
if (isset($_GET['ar_root_5']) && $_GET['ar_root_5']) $suche_nach[] = GGA_ARABIC_ROOT_5.' = '.$_GET['ar_root_5'];
if (isset($_GET['ar_stem']) && $_GET['ar_stem']) $suche_nach[] = GGA_ARABIC_STEM.' = '.$_GET['ar_stem'];
if (isset($_GET['ar_pos']) && $_GET['ar_pos']) $suche_nach[] = GGA_ARABIC_POS.' = '.$_GET['ar_pos'];
if (isset($_GET['ar_expression']) && $_GET['ar_expression']) $suche_nach[] = GGA_ARABIC_EXPRESSION.' = '.$_GET['ar_expression'];
if (isset($_GET['ar_reference']) && $_GET['ar_reference']) $suche_nach[] = GGA_ARABIC_REFERENCE.' = '.$_GET['ar_reference'];
$template_content .= implode(', ',$suche_nach);
$template_content .= '</em></p>';

$template_content .= $dbi->getListView('gga_wordsbyquery',$words_query);
$template_content .= '<div class="buttons">';
$template_content .= createButton (DBI_MODIFY_SEARCH,'search.php?'.$dbi->getUserVar('querystring'),'icon search');
$template_content .= createButton (DBI_NEW_SEARCH,'search.php','icon search');
$template_content .= '</div>';

$template_sidebar = '<h3>'.DBI_HELP.'</h3>';
$template_sidebar .= $dbi->getHelptext_HTML ('results');

// call template
require_once 'templates/ini.php';

?>
