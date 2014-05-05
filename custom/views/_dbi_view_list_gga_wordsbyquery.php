<?php

class View_List_GGA_WordsByQuery extends View_List {

	// CONSTRUCTOR ---------------------------------------------------------------
	
	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_List_GGA_WordsByQuery ($args[0]);
		}
	}

	// VIEW ----------------------------------------------------------------------	

	public function get_HTML ($words_query) {
		$html = '';
		if (mysql_num_rows($words_query) > 0) {
			// SORT OPTIONS
			$this->addSortOption (GGA_GREEK_LEXEME,'gr_lexeme','ASC','DESC');
			$this->addSortOption (GGA_ARABIC_LEXEME,'ar_lexeme','ASC','DESC');
			$this->addSortOption (GGA_SOURCE,'source_name','ASC','DESC');
			// LIST
			$html .= $this->getBrowseOptions_HTML ();
			$html .= $this->getSortOptions_HTML ();
			// LIST OF WORDS
			$html .= '<ul class="icons word">';
			while ($word = mysql_fetch_object($words_query)) {
				$html .= '<li>';
				$html .= $this->Creator->getWordLink_HTML ($word);
				$html .= ' <em>('.$word->source_name.')</em></li>'.PHP_EOL;
				$html .= '</li>';
				$html .= PHP_EOL;
			}
			$html .= '</ul>';
		}
		else {
			$html .= '<p>'.DBI_NO_RESULTS.'</p>';
		}
		return $html;
	}
	
}

?>
