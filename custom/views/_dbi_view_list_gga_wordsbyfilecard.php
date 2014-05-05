<?php
// last known update: 2013-01-25

class View_List_GGA_WordsByFilecard extends View_List {

	// CONSTRUCTOR ---------------------------------------------------------------
	
	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_List_GGA_WordsByFilecard ($args[0]);
		}
	}

	// VIEW ----------------------------------------------------------------------	

	public function get_HTML () {
		$html = '';
		// GET FILECARDS
		$words_querystring = 'SELECT g.* FROM glossary g WHERE g.filecard_id='.$this->Creator->getUserVar('filecard_id').' ORDER BY word_id';
		$words_query = mysql_query($words_querystring);
		// SET USER VARS
		$this->Creator->setUserVar('total_words',mysql_num_rows($words_query));
		// LIST OF WORDS
		$html .= '<h3>'.GGA_WORDS_ON_FILECARD.'</h3>';
		$html .= '<ul class="icons word no-indent">';
		while ($word = mysql_fetch_object($words_query)) {
			$html .= '<li>';
			$html .= $this->Creator->getWordLink_HTML ($word);
			$html .= '</li>';
			$html .= PHP_EOL;
		}
		$html .= '</ul>';
		if ($this->Creator->checkUserPermission('edit')) {
			$html .= createSmallButton (GGA_ADD_WORD,'gga_edit_word.php?source_id='.$this->Creator->getUserVar('source_id').'&filecard_id='.$this->Creator->getUserVar('filecard_id'),'icon addWord');
		}
		return $html;
	}

}

?>
