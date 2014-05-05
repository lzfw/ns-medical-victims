<?php

class View_Record_GGA_Word extends View_Record {
	
	// CONSTRUCTOR ---------------------------------------------------------------
	
	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_Record_GGA_Word ($args[0]);
		}
	}
	
	// VIEW ----------------------------------------------------------------------	
	
	public function getLinkedField_HTML ( ) {
		// getLinkedField_HTML ( name, content [, title] )
		$args = func_get_args();
		switch (func_num_args()) {
			case 2: return ($args[1]?('<a href="results.php?'.$args[0].'='.$args[1].'">'.$args[1].'</a>'):GGA_NOT_SPECIFIED_HTML); break;
			case 3: return ($args[1]?('<a href="results.php?'.$args[0].'='.$args[1].'">'.$args[2].'</a>'):GGA_NOT_SPECIFIED_HTML);
			default: return false; break;
		}
	}
	
	public function get_HTML () {
		$html = '';
		// GET WORD
		$word_querystring = "
			SELECT
				g.*,LEFT(g.gr_lexeme,1) AS gr_initial,LEFT(g.ar_lexeme,1) AS ar_initial,
				s.abbreviation AS source_abbreviation,s.name AS source_name,
				f.name AS filecard_name, f.folder AS filecard_folder, f.extension AS filecard_extension,
				gp.title AS gr_pos_title,
				ap.title AS ar_pos_title,
				uc.display_name AS user_created,
				um.display_name AS user_modified
			FROM glossary g
				LEFT OUTER JOIN sources s USING (source_id)
				LEFT OUTER JOIN filecards f USING (filecard_id)
				LEFT OUTER JOIN greek_pos gp ON (g.gr_pos = gp.name)
				LEFT OUTER JOIN arabic_pos ap ON (g.ar_pos = ap.name)
				LEFT OUTER JOIN users uc
				ON uc.user_id = g.user_created_id
				LEFT OUTER JOIN users um
				ON um.user_id = g.user_modified_id
			WHERE g.word_id={$this->Creator->getUserVar('word_id')}
		";
		if ($word_query = mysql_query($word_querystring)) {
			if ($word = mysql_fetch_object($word_query)) {
				// COUNT ALL WORDS FROM SOURCE
				$count_words_querystring = "SELECT COUNT(word_id) AS total FROM glossary WHERE source_id={$word->source_id}";
				$count_words_query = mysql_query($count_words_querystring);
				$count_words = mysql_fetch_object($count_words_query);
				// SET USER VARS
				$this->Creator->setUserVar('word_id',$word->word_id);
				$this->Creator->setUserVar('source_id',$word->source_id);
				$this->Creator->setUserVar('source_name',$word->source_name);
				$this->Creator->setUserVar('filecard_id',$word->filecard_id);
				$this->Creator->setUserVar('filecard_name',$word->filecard_name);
				$this->Creator->setUserVar('ar_initial',$word->ar_initial);
				$this->Creator->setUserVar('ar_lexeme',$word->ar_lexeme);
				$this->Creator->setUserVar('gr_initial',$word->gr_initial);
				$this->Creator->setUserVar('gr_lexeme',$word->gr_lexeme);
				$this->Creator->setUserVar('total_words',$count_words->total);
				// BREADCRUMBS
				$this->Creator->addBreadcrumb (GGA_GLOSSARY,'glossary.php');
				$this->Creator->addBreadcrumb ($this->Creator->getCurrentWord_HTML());
				// OPTIONS
				if ($this->Creator->checkUserPermission('edit')) {
					$this->Creator->addOption (GGA_EDIT_WORD,'gga_edit_word.php?word_id='.$word->word_id,'icon editWord');
					//$this->Creator->addOption (GGA_DUPLICATE_WORD,'gga_duplicate_word.php?word_id='.$word->word_id,'icon duplicateWord');
					//$this->Creator->addOption (GGA_REMOVE_WORD,'gga_remove_word.php?word_id='.$word->word_id,'icon removeWord');
				}
				if (in_array($word->word_id,$_SESSION[BOOKMARKS])) {
					$this->Creator->addOption (DBI_REMOVE_BOOKMARK,'bookmarks.php?action=remove&id='.$word->word_id.'&return=yes','icon removeBookmark');
				}
				else {
					$this->Creator->addOption (DBI_ADD_BOOKMARK,'bookmarks.php?action=add&id='.$word->word_id.'&return=yes','icon addBookmark');
				}
				// WORD RECORD
				$html .= '<table class="columns">'.PHP_EOL;
				$html .= '<colgroup><col width="120"><col width="220"><col width="220"></colgroup>'.PHP_EOL;
				// headlines
				$html .= '<tr>'.PHP_EOL;
				$html .= '<th>'.GGA_LANGUAGE.'</th>';
				$html .= '<td class="boxed">'.GGA_GREEK.'</td>'.PHP_EOL;
				$html .= '<td class="boxed align-right">'.GGA_ARABIC.'</td>'.PHP_EOL;
				$html .= '</tr>'.PHP_EOL;
				// content
				// lexical information
				$html .= '<tr><th>'.GGA_LEXEME.'</th>'.
					'<td>'.$this->getLinkedField_HTML ('gr_lexeme',$word->gr_lexeme).
					// translit tool
					//' <img class="icon" src="icons/fugue/edit-shadow.png" alt="transcription" title="'.gr_translit_ws_txt($word->gr_lexeme).'">'.
					'</td>'.
					'<td class="align-right">'.$this->getLinkedField_HTML ('ar_lexeme',$word->ar_lexeme).'</td>'.
					'</tr>'.PHP_EOL;
					$html .= '<tr><th>'.GGA_ROOT_STEM.'</th>'.
					'<td></td>'.
					'<td class="align-right">'.
					'<a href="results.php?'.
					($word->ar_root_1?'&ar_root_1='.$word->ar_root_1:'').
					($word->ar_root_2?'&ar_root_2='.$word->ar_root_2:'').
					($word->ar_root_3?'&ar_root_3='.$word->ar_root_3:'').
					($word->ar_root_4?'&ar_root_4='.$word->ar_root_4:'').
					($word->ar_root_5?'&ar_root_5='.$word->ar_root_5:'').
					'">'.
					($word->ar_root_1?$word->ar_root_1:GGA_NOT_SPECIFIED_HTML).' '.
					($word->ar_root_2?$word->ar_root_2:GGA_NOT_SPECIFIED_HTML).' '.
					($word->ar_root_3?$word->ar_root_3:GGA_NOT_SPECIFIED_HTML).' '.
					($word->ar_root_4?$word->ar_root_4:GGA_NOT_SPECIFIED_HTML).' '.
					($word->ar_root_5?$word->ar_root_5:GGA_NOT_SPECIFIED_HTML).
					'</a>'.' / '.
					$this->getLinkedField_HTML('ar_stem',$word->ar_stem,roman_numeral($word->ar_stem)).'</td>'.
					'</tr>'.PHP_EOL;
				// grammar
				$html .= '<tr><th>'.GGA_PART_OF_SPEECH.'</th>'.
					'<td>'.$this->getLinkedField_HTML ('gr_pos',$word->gr_pos,$word->gr_pos_title).'</td>'.
					'<td class="align-right">'.$this->getLinkedField_HTML ('ar_pos',$word->ar_pos,$word->ar_pos_title).'</td>'.
					'</tr>'.PHP_EOL;
				// context
				$html .= '<tr><th>'.GGA_EXPRESSION.'</th>'.
					'<td>'.($word->gr_expression?$word->gr_expression:GGA_NOT_SPECIFIED_HTML).'</td>'.
					'<td class="align-right">'.($word->ar_expression?$word->ar_expression:GGA_NOT_SPECIFIED_HTML).'</td>'.
					'</tr>'.PHP_EOL;
				// critical apparatus
				$html .= '<tr><th>'.GGA_ANNOTATION.'</th>'.
					'<td>'.($word->gr_annotation?$word->gr_annotation:GGA_NOT_SPECIFIED_HTML).'</td>'.
					'<td class="align-right">'.($word->ar_annotation?$word->ar_annotation:GGA_NOT_SPECIFIED_HTML).'</td></tr>'.PHP_EOL;
				$html .= '<tr><th>'.GGA_QUOTATION.'</th>'.
					'<td>'.($word->gr_quotation?$word->gr_quotation:GGA_NOT_SPECIFIED_HTML).'</td>'.
					'<td class="align-right">'.($word->ar_quotation?$word->ar_quotation:GGA_NOT_SPECIFIED_HTML).'</td>'.
					'</tr>'.PHP_EOL;
				$html .= '<tr><th>'.GGA_REFERENCE.'</th>'.
					'<td>'.$this->getLinkedField_HTML('gr_reference',$word->gr_reference).'</td>'.
					'<td class="align-right">'.$this->getLinkedField_HTML('ar_reference',$word->ar_reference).'</td>'.
					'</tr>'.PHP_EOL;
				$html .= '</table>'.PHP_EOL;
				if ($this->Creator->checkUserPermission('edit')) {
					$html .= getDateUserStamp_HTML (DBI_STAMP_CREATED,$word->date_created,$word->user_created);
					$html .= getDateUserStamp_HTML (DBI_STAMP_MODIFIED,$word->date_modified,$word->user_modified);
				}
				// FILECARD
				// get next filecard
				$next_word_querystring = "
					SELECT f.filecard_id, f.name, g.word_id
					FROM filecards f
					LEFT OUTER JOIN glossary g USING (filecard_id)
					WHERE f.folder = '$word->filecard_folder'
						AND f.name > '$word->filecard_name'
					ORDER BY f.name ASC, g.word_id ASC
					LIMIT 1
				";
				$next_word_query = mysql_query($next_word_querystring);
				if (mysql_num_rows($next_word_query) > 0)
					$next_word = mysql_fetch_object ($next_word_query);
				// get previous filecard
				$prev_word_querystring = "
					SELECT f.filecard_id, f.name, g.word_id
					FROM filecards f
					LEFT OUTER JOIN glossary g USING (filecard_id)
					WHERE f.folder = '$word->filecard_folder'
						AND f.name < '$word->filecard_name'
					ORDER BY f.name DESC, g.word_id ASC
					LIMIT 1
				";
				$prev_word_query = mysql_query($prev_word_querystring);
				if (mysql_num_rows($prev_word_query) > 0)
					$prev_word = mysql_fetch_object ($prev_word_query);
				// link to next or previous filecard
				$html .= '<p>';
				if (isset($prev_word)) $html .= createButton (DBI_PREVIOUS_SYMBOL.DBI_RESULTS_PREVIOUS,'glossary.php?id='.$prev_word->word_id,NULL,$prev_word->name);
				if (isset($next_word)) $html .= createButton (DBI_RESULTS_NEXT.DBI_NEXT_SYMBOL,'glossary.php?id='.$next_word->word_id,NULL,$next_word->name);
				$html .= '</p>';
				// FILECARD IMAGE
				$filecard_querystring = "
					SELECT f.*
					FROM filecards f
					WHERE f.filecard_id={$this->Creator->getUserVar('filecard_id')}
				";
				if ($filecard_query = mysql_query($filecard_querystring)) {
					if ($filecard = mysql_fetch_object($filecard_query)) {
						$html .= $this->Creator->getFilecardViewer_HTML ($filecard);
					}
				}
			}
			else {
				$html .= DBI_NO_RESULTS;
			}
		}
		else {
			$html .= DBI_ERROR_QUERY;
		}
		return $html;
	}
	
}

?>
