<?php

class View_Record_GGA_Source extends View_Record {

	// CONSTRUCTOR ---------------------------------------------------------------
	
	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_Record_GGA_Source ($args[0]);
		}
	}

	// VIEW ----------------------------------------------------------------------	

	public function get_HTML () {
		$html = '';
		// GET SOURCE
		$source_querystring = "
			SELECT
				s.*,
				uc.display_name AS user_created,
				um.display_name AS user_modified
			FROM sources s
				LEFT OUTER JOIN dbi_users uc
				ON uc.user_id = s.user_created_id
				LEFT OUTER JOIN dbi_users um
				ON um.user_id = s.user_modified_id
			WHERE source_id={$this->Creator->getUserVar('source_id')}";
		if ($source_query = mysql_query($source_querystring)) {
			if ($source = mysql_fetch_object($source_query)) {
				// COUNT FILECARDS
				$count_filecards_querystring = "SELECT COUNT(filecard_id) AS total FROM filecards WHERE source_id={$this->Creator->getUserVar('source_id')}";
				$count_filecards_query = mysql_query($count_filecards_querystring);
				$count_filecards = mysql_fetch_object($count_filecards_query);
				// COUNT WORDS
				$count_words_querystring = "SELECT COUNT(word_id) AS total FROM glossary WHERE source_id={$this->Creator->getUserVar('source_id')}";
				$count_words_query = mysql_query($count_words_querystring);
				$count_words = mysql_fetch_object($count_words_query);
				// SET USER VARS
				$this->Creator->setUserVar('source_id',$source->source_id);
				$this->Creator->setUserVar('source_name',$source->name);
				$this->Creator->setUserVar('total_filecards',$count_filecards->total);
				$this->Creator->setUserVar('total_words',$count_words->total);
				// BREADCRUMBS
				$this->Creator->addBreadcrumb (GGA_SOURCES,'sources.php');
				$this->Creator->addBreadcrumb ($source->name);
				// OPTIONS
				if ($this->Creator->checkUserPermission('admin')) {
					$this->Creator->addOption (GGA_EDIT_SOURCE,'gga_edit_source.php?source_id='.$this->Creator->getUserVar('source_id'),'icon editSource');
					$this->Creator->addOption (GGA_REMOVE_SOURCE,'gga_remove_source.php?source_id='.$this->Creator->getUserVar('source_id'),'icon removeSource');
				}
				// DISPLAY SOURCE RECORD
				if ($source->abbreviation) $html .= '<p>'.GGA_ABBREVIATION.': <em>'.$source->abbreviation.'</em></p>';
				if ($source->description) $html .= SimpleMarkup_HTML($source->description).PHP_EOL;
				if ($source->related_source_id) $html .= '<p class="arrow-right"><a href="sources.php?id='.$source->related_source_id.'">'.GGA_SOURCE_RELATED.'</a></p>';
				if ($this->Creator->checkUserPermission('edit')) {
					$html .= getDateUserStamp_HTML (DBI_STAMP_CREATED,$source->date_created,$source->user_created);
					$html .= getDateUserStamp_HTML (DBI_STAMP_MODIFIED,$source->date_modified,$source->user_modified);
				}
				// LINKS TO FILECARDS AND TO LETTER LIST
				$html .= createBacklink (GGA_SOURCES.' ('.GGA_ALPHABETICAL_ORDER.')','sources.php','icon sources');
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
