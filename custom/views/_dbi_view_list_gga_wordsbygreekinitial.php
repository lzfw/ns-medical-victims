<?php

class View_List_GGA_WordsByGreekInitial extends View_List {

  // CONSTRUCTOR ---------------------------------------------------------------
  
  static public function create () {
    // create ( Creator )
    $args = func_get_args();
    switch (func_num_args()) {
      case 1: return new View_List_GGA_WordsByGreekInitial ($args[0]);
    }
  }

  // VIEW ----------------------------------------------------------------------	

	public function get_HTML () {
		$html = '';
		// QUERY
		$glossary_querystring = "
			SELECT g.*, s.name AS source_name
			FROM glossary g
			LEFT OUTER JOIN sources s USING (source_id)
			WHERE g.gr_lexeme LIKE '{$this->Creator->getUserVar('gr_initial')}%'
			ORDER BY {$this->Creator->getUserVar('sort')} {$this->Creator->getUserVar('order')}
			LIMIT {$this->Creator->getUserVar('skip')},".DBI_LIST_ROWS_PAGE;
		$glossary_query = mysql_query($glossary_querystring);
		// TOTAL WORDS
		$count_words_querystring = "SELECT COUNT(word_id) AS total FROM glossary g
			WHERE g.gr_lexeme LIKE '{$this->Creator->getUserVar('gr_initial')}%'
		";
		$count_words_query = mysql_query($count_words_querystring);
		$count_words = mysql_fetch_object($count_words_query);
		// BREADCRUMBS
		$this->Creator->addBreadcrumb (GGA_GLOSSARY,'glossary.php');
		$this->Creator->addBreadcrumb (GGA_GREEK,'glossary.php');
		$this->Creator->addBreadcrumb ($this->Creator->getUserVar('gr_initial'));
		// OPTIONS
		if ($this->Creator->UserPermission('edit')) {
			$this->Creator->addOption (GGA_ADD_WORD,'gga_edit_word.php',ICON_ADD_WORD);
		}
		// SORT OPTIONS
		$this->Creator->setUserVar('total_results',$count_words->total);
		$this->addSortOption (GGA_GREEK_LEXEME,'gr_lexeme','ASC','DESC');
		$this->addSortOption (GGA_ARABIC_LEXEME,'ar_lexeme','ASC','DESC');
		$this->addSortOption (GGA_SOURCE,'source_name','ASC','DESC');
		// LIST
		$html .= $this->getBrowseOptions_HTML ();
		$html .= $this->getSortOptions_HTML ();
		$html .= '<ul class="icons word">'.PHP_EOL;
		while ($word = mysql_fetch_object($glossary_query)) {
			$html .= '<li>';
			$html .= $this->Creator->getWordLink_HTML($word);
			$html .= ' <em>('.$word->source_name.')</em></li>'.PHP_EOL;
		}	
		$html .= '</ul>'.PHP_EOL;
		// LINKS TO GLOSSARY
		$html .= createBackLink (GGA_GLOSSARY,'glossary.php');
		return $html;
	}

}

?>
