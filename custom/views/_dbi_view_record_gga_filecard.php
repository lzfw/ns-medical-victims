<?php

class View_Record_GGA_Filecard extends View_Record {

	// CONSTRUCTOR ---------------------------------------------------------------
	
	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_Record_GGA_Filecard ($args[0]);
		}
	}

	// VIEW ----------------------------------------------------------------------	

	public function get_HTML () {
		$html = '';
		// get filecard
		$filecard_querystring = "
			SELECT
				f.*,LEFT(f.name,2) AS initial,
				s.name AS source_name,
				uc.display_name AS user_created,
				um.display_name AS user_modified
			FROM filecards f
				LEFT OUTER JOIN sources s USING (source_id)
				LEFT OUTER JOIN users uc
				ON uc.user_id = f.user_created_id
				LEFT OUTER JOIN users um
				ON um.user_id = f.user_modified_id
			WHERE filecard_id={$this->Creator->getUserVar('filecard_id')}
		";
		if ($filecard_query = mysql_query($filecard_querystring)) {
			if ($filecard = mysql_fetch_object($filecard_query)) {
				// get words
				$words_querystring = "
					SELECT g.word_id, g.gr_lexeme, g.ar_lexeme
					FROM glossary g
					WHERE g.filecard_id=$filecard->filecard_id
				";
				$words_query = mysql_query($words_querystring);
				$words_count = mysql_num_rows($words_query);
				// get next filecard
				$next_filecard_querystring = "
					SELECT f.filecard_id, f.name
					FROM filecards f
					WHERE f.folder = '$filecard->folder'
						AND f.name > '$filecard->name'
					ORDER BY f.name ASC
					LIMIT 1
				";
				$next_filecard_query = mysql_query($next_filecard_querystring);
				if (mysql_num_rows($next_filecard_query) > 0)
					$next_filecard = mysql_fetch_object ($next_filecard_query);
				// get previous filecard
				$previous_filecard_querystring = "
					SELECT f.filecard_id, f.name
					FROM filecards f
					WHERE f.folder = '$filecard->folder'
						AND f.name < '$filecard->name'
					ORDER BY f.name DESC
					LIMIT 1
				";
				$previous_filecard_query = mysql_query($previous_filecard_querystring);
				if (mysql_num_rows($previous_filecard_query) > 0)
					$previous_filecard = mysql_fetch_object ($previous_filecard_query);
				// set user vars
				$this->Creator->setUserVar('filecard_id',$filecard->filecard_id);
				$this->Creator->setUserVar('filecard_initial',$filecard->initial);
				$this->Creator->setUserVar('filecard_name',$filecard->name);
				$this->Creator->setUserVar('source_id',$filecard->source_id);
				$this->Creator->setUserVar('source_name',$filecard->source_name);
				$this->Creator->setUserVar('total_words',$words_count);
				// breadcrumbs
				$this->Creator->addBreadcrumb (GGA_FILECARDS,'filecards.php');
				$this->Creator->addBreadcrumb ($filecard->folder,'filecards.php?folder='.$filecard->folder);
				$this->Creator->addBreadcrumb ($filecard->name);
				// options
				if ($this->Creator->checkUserPermission('edit')) {
					$this->Creator->addOption (GGA_EDIT_FILECARD,'gga_edit_filecard.php?filecard_id='.$this->Creator->getUserVar('filecard_id'),'icon editFilecard');
					$this->Creator->addOption (GGA_REMOVE_FILECARD,'gga_remove_filecard.php?filecard_id='.$this->Creator->getUserVar('filecard_id'),'icon removeFilecard');
				}
				// output
				// link to next or previous filecard
				$html .= '<p>';
				if (isset($previous_filecard)) $html .= createButton (DBI_PREVIOUS_SYMBOL.DBI_RESULTS_PREVIOUS,'filecards.php?id='.$previous_filecard->filecard_id,NULL,$previous_filecard->name);
				if (isset($next_filecard)) $html .= createButton (DBI_RESULTS_NEXT.DBI_NEXT_SYMBOL,'filecards.php?id='.$next_filecard->filecard_id,NULL,$next_filecard->name);
				$html .= '</p>';
				$html .= $this->Creator->getFilecardViewer_HTML ($filecard);
				if ($this->Creator->checkUserPermission('edit')) {
					$html .= getDateUserStamp_HTML (DBI_STAMP_CREATED,$filecard->date_created,$filecard->user_created);
					$html .= getDateUserStamp_HTML (DBI_STAMP_MODIFIED,$filecard->date_modified,$filecard->user_modified);
				}
				// link to filecards by source
				$html .= createBacklink (GGA_FOLDER.': '.$filecard->folder,'filecards.php?folder='.$filecard->folder,'icon filecards');
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
