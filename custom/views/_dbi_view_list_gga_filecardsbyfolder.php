<?php
// last known update: 2013-01-25

class View_List_GGA_FilecardsByFolder extends View_List {

	// CONSTRUCTOR ---------------------------------------------------------------
	
	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_List_GGA_FilecardsByFolder ($args[0]);
		}
	}

	// VIEW ----------------------------------------------------------------------	

	public function get_HTML () {
		$html = '';
		// GET FOLDER
		$folder_querystring = "
			SELECT f.folder AS name,LEFT(f.folder,2) AS initial,COUNT(f.filecard_id) AS filecards
			FROM filecards f WHERE folder='{$this->Creator->getUserVar('folder')}'
			GROUP BY folder";
		if ($folder_query = mysql_query($folder_querystring)) {
			if ($folder = mysql_fetch_object($folder_query)) {
				// GET FILECARDS
				$filecards_querystring = "
					SELECT f.* FROM filecards f WHERE f.folder='{$folder->name}'
					ORDER BY {$this->Creator->getUserVar('sort')} {$this->Creator->getUserVar('order')}
					LIMIT {$this->Creator->getUserVar('skip')},".DBI_LIST_ROWS_PAGE;
				$filecards_query = mysql_query($filecards_querystring);
				// SET USER VARS
				$this->Creator->setUserVar('folder_initial',$folder->initial);
				$this->Creator->setUserVar('total_filecards',$folder->filecards);
				// BREADCRUMBS
				$this->Creator->addBreadcrumb (GGA_FILECARDS,'filecards.php');
				$this->Creator->addBreadcrumb ($folder->name);
				// SORT OPTIONS
				$this->Creator->setUserVar('total_results',$folder->filecards);
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
				$html .= createBacklink (GGA_FOLDERS.' ('.GGA_ALPHABETICAL_ORDER.')','filecards.php');
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
