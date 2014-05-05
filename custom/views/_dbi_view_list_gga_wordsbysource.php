<?php
// last known update: 2013-01-25

class View_List_GGA_WordsBySource extends View_List {

	// CONSTRUCTOR ---------------------------------------------------------------
	
	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_List_GGA_WordsBySource ($args[0]);
		}
	}

	// VIEW ----------------------------------------------------------------------	

	public function get_HTML () {
		$html = '';
		// GET SOURCE
		$source_querystring = "
			SELECT s.*
			FROM sources s
			WHERE source_id={$this->Creator->getUserVar('source_id')}
		";
		if ($source_query = mysql_query($source_querystring)) {
			if ($source = mysql_fetch_object($source_query)) {
				// GET WORDS
				$words_querystring = "
					SELECT g.*
					FROM glossary g
					WHERE g.source_id={$this->Creator->getUserVar('source_id')}
					ORDER BY
						g.gr_lexeme='', g.ar_lexeme='',
					{$this->Creator->getUserVar('sort')} {$this->Creator->getUserVar('order')}
					LIMIT {$this->Creator->getUserVar('skip')},".DBI_LIST_ROWS_PAGE;
				$words_query = mysql_query($words_querystring);
				// COUNT WORDS
				$count_words_querystring = "SELECT COUNT(g.word_id) AS total FROM glossary g WHERE source_id={$this->Creator->getUserVar('source_id')}";
				$count_words_query = mysql_query($count_words_querystring);
				$count_words = mysql_fetch_object($count_words_query);
				// COUNT FILECARDS
				$count_filecards_querystring = "SELECT COUNT(filecard_id) AS total FROM filecards WHERE source_id={$this->Creator->getUserVar('source_id')}";
				$count_filecards_query = mysql_query($count_filecards_querystring);
				$count_filecards = mysql_fetch_object($count_filecards_query);
				// SET USER VARS
				$this->Creator->setUserVar('source_id',$source->source_id);
				$this->Creator->setUserVar('source_name',$source->name);
				$this->Creator->setUserVar('total_words',$count_words->total);
				$this->Creator->setUserVar('total_filecards',$count_filecards->total);
				// BREADCRUMBS
				$this->Creator->addBreadcrumb (GGA_SOURCES,'sources.php');
				$this->Creator->addBreadcrumb ($source->name,'sources.php?id='.$this->Creator->getUserVar('source_id'));
				$this->Creator->addBreadcrumb (GGA_WORDS);
				// OPTIONS
				if ($this->Creator->checkUserPermission('edit')) {
					$this->Creator->addOption (GGA_ADD_WORD,'gga_edit_word.php?source_id='.$this->Creator->getUserVar('source_id'),'icon addWord');
				}
				// SORT OPTIONS
				$this->Creator->setUserVar('total_results',$count_words->total);
				$this->addSortOption (GGA_GREEK_LEXEME,'gr_lexeme','ASC','DESC');
				$this->addSortOption (GGA_ARABIC_LEXEME,'ar_lexeme','ASC','DESC');
				// LIST OF FILECARDS
				$html .= $this->getBrowseOptions_HTML ();
				$html .= $this->getSortOptions_HTML ();
				// LIST OF WORDS
				$html .= '<ul class="icons word">'.PHP_EOL;
				while ($word = mysql_fetch_object($words_query)) {
					$html .= '<li>';
					$html .= $this->Creator->getWordLink_HTML ($word);
					$html .= '</li>';
					$html .= PHP_EOL;
				}
				$html .= '</ul>'.PHP_EOL;
				$html .= createBacklink (GGA_SOURCE.': '.$source->name,'sources.php?id='.$source->source_id);
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
