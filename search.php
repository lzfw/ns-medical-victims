<?php
// CMS file: search form (public)
// last known update: 2013-01-22

require_once 'setup/ini.php';
require_once 'flotilla/ini.php';

// template variables
$template_title = '';
$template_content = '';
$template_sidebar = '';

$form = new Form ('search','results.php','GET');

$form->addField ('header_greek',STATIC_TEXT,'<h3>'.GGA_GREEK.'</h3>');

$form->addField ('source_id',SELECT)
	->setLabel (GGA_WORD_SOURCE)
	->addOption (0,GGA_NOT_SPECIFIED)
	->addOptionsFromTable ('sources','source_id','name'); 

$form->addField ('gr_lexeme',TEXT,120)
	->setLabel (GGA_GREEK_LEXEME)
	->setLanguage ('gr')
	->setClass ('keyboardInput');

$form->addField ('gr_pos',SELECT)
	->setLabel (GGA_GREEK_POS)
	->addOption ('',GGA_NOT_SPECIFIED)
	->addOptionsFromTable ('greek_pos','name','title');

$form->addField ('gr_expression',TEXT,250)
	->setLabel (GGA_GREEK_EXPRESSION)
	->setLanguage ('gr')
	->setClass ('keyboardInput');

$form->addField ('header_arabic',STATIC_TEXT,'<h3>'.GGA_ARABIC.'</h3>');

$form->addField ('ar_lexeme',TEXT,120)
	->setLabel (GGA_ARABIC_LEXEME)
	->setLanguage ('ar')
	->setClass ('keyboardInput');

$form->addField ('ar_root_1',SELECT)
	->setLabel (GGA_ARABIC_ROOT_STEM)
	->addOption ('',GGA_NOT_SPECIFIED)
	->addOptionsFromTable ('arabic_letters','latin_mono','latin_mono');
$form->addField ('ar_root_2',SELECT)
	->appendTo ('ar_root_1')
	->addOption ('',GGA_NOT_SPECIFIED)
	->addOptionsFromTable ('arabic_letters','latin_mono','latin_mono');
$form->addField ('ar_root_3',SELECT)
	->appendTo ('ar_root_1')
	->addOption ('',GGA_NOT_SPECIFIED)
	->addOptionsFromTable ('arabic_letters','latin_mono','latin_mono');
$form->addField ('ar_root_4',SELECT)
	->appendTo ('ar_root_1')
	->addOption ('',GGA_NOT_SPECIFIED)
	->addOptionsFromTable ('arabic_letters','latin_mono','latin_mono');
$form->addField ('ar_root_5',SELECT)
	->appendTo ('ar_root_1')
	->addOption ('',GGA_NOT_SPECIFIED)
	->addOptionsFromTable ('arabic_letters','latin_mono','latin_mono');

$form->addField ('ar_stem',SELECT)
	->appendTo ('ar_root_1')
	->setLabel (GGA_STEM)
	->addOption ('',GGA_NOT_SPECIFIED)
	->addOption (1,'I')
	->addOption (2,'II')
	->addOption (3,'III')
	->addOption (4,'IV')
	->addOption (5,'V')
	->addOption (6,'VI')
	->addOption (7,'VII')
	->addOption (8,'VIII')
	->addOption (9,'IX')
	->addOption (10,'X')
	->addOption (11,'XI')
	->addOption (12,'XII');

$form->addField ('ar_pos',SELECT)
	->setLabel (GGA_ARABIC_POS)
	->addOption ('',GGA_NOT_SPECIFIED)
	->addOptionsFromTable ('arabic_pos','name','title');
$form->addField ('ar_expression',TEXT,250)
	->setLabel (GGA_ARABIC_EXPRESSION)
	->setLanguage ('ar-la')
	->setClass ('keyboardInput');

$form
	->addButton (BACK)
	->addButton (RESET)
	->addButton (SUBMIT,DBI_SEARCH);

// breadcrumbs
$dbi->addBreadcrumb (DBI_SEARCH);

// template variables
$template_title .= DBI_SEARCH;
$template_content .= getVirtualKeyboard ();
$template_content .= $form->run ();
$template_sidebar .= '<h3>'.DBI_HELP.'</h3>';
$template_sidebar .= $dbi->getHelptext_HTML ('search');

// call template
require_once 'templates/ini.php';

?>
