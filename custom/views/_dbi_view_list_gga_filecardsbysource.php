<?php
// last known update: 2013-01-25

class View_List_GGA_FilecardsBySource extends View_List {

	// CONSTRUCTOR ---------------------------------------------------------------
	
	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_List_GGA_FilecardsBySource ($args[0]);
		}
	}

	// VIEW ----------------------------------------------------------------------	

	public function get_HTML () {
		$html = '';
		// GET SOURCE
		$source_querystring = "SELECT s.*,LEFT(s.name,2) AS initial FROM sources s WHERE source_id={$this->Creator->getUserVar('source_id')}";
		if ($source_query = mysql_query($source_querystring)) {
			if ($source = mysql_fetch_object($source_query)) {
				// GET FILECARDS
				$filecards_querystring = "
					SELECT f.* FROM filecards f WHERE f.source_id=$source->source_id
					ORDER BY {$this->Creator->getUserVar('sort')} {$this->Creator->getUserVar('order')}
					LIMIT {$this->Creator->getUserVar('skip')},".DBI_LIST_ROWS_PAGE;
				$filecards_query = mysql_query($filecards_querystring);
				// TOTAL FILECARDS
				$count_filecards_querystring = "
					SELECT COUNT(filecard_id) AS total FROM filecards
					WHERE source_id={$this->Creator->getUserVar('source_id')}";
				$count_filecards_query = mysql_query($count_filecards_querystring);
				$count_filecards = mysql_fetch_object($count_filecards_query);
				// TOTAL WORDS
				$count_words_querystring = "
					SELECT COUNT(word_id) AS total FROM glossary
					WHERE source_id={$this->Creator->getUserVar('source_id')}";
				$count_words_query = mysql_query($count_words_querystring);
				$count_words = mysql_fetch_object($count_words_query);
				// SET USER VARS
				$this->Creator->setUserVar('source_id',$source->source_id);
				$this->Creator->setUserVar('source_initial',$source->initial);
				$this->Creator->setUserVar('source_name',$source->name);
				$this->Creator->setUserVar('total_filecards',$count_filecards->total);
				$this->Creator->setUserVar('total_words',$count_words->total);
				// BREADCRUMBS
				$this->Creator->addBreadcrumb (GGA_SOURCES,'sources.php');
				//$this->Creator->addBreadcrumb ($this->Creator->getUserVar('source_initial'),'sources.php?initial='.$this->Creator->getUserVar('source_initial'));
				$this->Creator->addBreadcrumb ($source->name,'sources.php?id='.$this->Creator->getUserVar('source_id'));
				$this->Creator->addBreadcrumb (GGA_FILECARDS);
				// SORT OPTIONS
				$this->Creator->setUserVar('total_results',$count_filecards->total);
				$this->addSortOption (GGA_FILECARD_NAME,'name','ASC','DESC');
				// LIST OF FILECARDS
				$html .= $this->getBrowseOptions_HTML ();
				$html .= $this->getSortOptions_HTML ();
				$html .= '<ul class="icons filecard">'.PHP_EOL;
				while ($filecard = mysql_fetch_object($filecards_query)) {
					$html .= '<li>'.PHP_EOL;
					$html .= '<a href="filecards.php?id='.$filecard->filecard_id.'">'.$filecard->name.'</a>'.PHP_EOL;
					$html .= '</li>'.PHP_EOL;
				}
				$html .= '</ul>';
				// LINK TO LIST OF INITIALS
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
