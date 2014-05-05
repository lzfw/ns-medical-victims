<?php
// CMS file: bookmarks (public)
// last known update: 2013-01-22

require_once 'setup/ini.php';
require_once 'flotilla/ini.php';
require_once 'dbi/classes/dbi_export_gga.php';

if (count($_SESSION[BOOKMARKS]) == 0)
	header ('Location: bookmarks.php');

// template variables
$template_title = '';
$template_content = '';
$template_sidebar = '';

// COMPOSE MAIL ----------------------------------------------------------------

switch (getUrlParameter('action')) {
	
	case 'send':
		// compose mail
		$bookmarks = implode(',',$_SESSION[BOOKMARKS]);
		$bookmarks_querystring = "
			SELECT
				g.*,
				s.abbreviation AS source_abbreviation,s.name AS source_name,
				f.name AS filecard_name, f.folder AS filecard_folder, f.extension AS filecard_extension,
				gp.title AS gr_pos_title,
				ap.title AS ar_pos_title
			FROM
				glossary g
				LEFT OUTER JOIN sources s USING (source_id)
				LEFT OUTER JOIN filecards f USING (filecard_id)
				LEFT OUTER JOIN greek_pos gp ON (g.gr_pos = gp.name)
				LEFT OUTER JOIN arabic_pos ap ON (g.ar_pos = ap.name)
			WHERE g.word_id IN ($bookmarks)
			ORDER BY g.gr_lexeme,g.ar_lexeme";
		$bookmarks_query = mysql_query($bookmarks_querystring);
		$mail_content = '';
		$row = 0;
		while ($word = mysql_fetch_object($bookmarks_query)) {
			$row++;
			$mail_content .= '('.$row.')'.PHP_EOL.getCitation($word,getPostValue('format')).PHP_EOL;
		}
		$mail_content = wordwrap($mail_content,70);
		$additional_headers = 'MIME-Version: 1.0'."\r\n";
		$additional_headers .= 'Content-type: text/plain; charset="UTF-8"'."\r\n";
		$additional_headers .= 'From: "'.GGA_BOOKMARKS_MAIL_NAME.'" <'.GGA_BOOKMARKS_MAIL_ADDRESS.">\r\n";
		$additional_parameters = NULL;
		// the following parameter depends on host
		//$additional_parameters = '-f '.$this->from_email;
		// now go
		$mail_content = GGA_BOOKMARKS_MAIL_HEADER.getPostValue('comment').PHP_EOL.PHP_EOL.$mail_content.GGA_BOOKMARKS_MAIL_SIGNATURE;
		if(empty($additional_parameters))
			mail(getPostValue('email'), GGA_BOOKMARKS_MAIL_SUBJECT, $mail_content, $additional_headers);
		else
			mail(getPostValue('email'), GGA_BOOKMARKS_MAIL_SUBJECT, $mail_content, $additional_headers, $this->additional_parameters);
		header ('Location: bookmarks.php');
		break;
	
	default:
		
		$form = new Form ('mail_bookmarks','?action=send');
		
		$form
			->setLabel(DBI_BOOKMARKS_ASK_EMAIL);
		
		$form->addField ('email',TEXT,30,REQUIRED)
			->setLabel (DBI_EMAIL)
			->addCondition (ALLOWED_CHARS,EMAIL);
		
		$form->addField ('format',RADIO,REQUIRED,'gr-ar')
			->setLabel (DBI_FORMAT)
			->addRadioButton ('gr-ar','Greek-Arabic')
			->addRadioButton ('ar-gr','Arabic-Greek')
			->addRadioButton ('list','Complete records with field names');
		
		$form->addField ('comment',TEXT,120)
			->setLabel (DBI_COMMENT);
		
		$form
			->addButton (BACK,DEFAULT_LABEL,'bookmarks.php')
			->addButton (SUBMIT,DBI_OK);
		
		$template_title .= DBI_SEND_BOOKMARKS;
		$template_content .= $form->run ();
				
		$template_sidebar .= '<h3>'.DBI_HELP.'</h3>';
		$template_sidebar .= $dbi->getHelptext_HTML ('bookmarks_send');
		
		require_once 'templates/ini.php';
		break;
}

?>
