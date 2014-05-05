<?php
// last known update: 2013-01-25

class View_List_GGA_Links extends View_List {

	// CONSTRUCTOR ---------------------------------------------------------------
	
	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_List_GGA_Links ($args[0]);
		}
	}

	// VIEW ----------------------------------------------------------------------	

	public function get_HTML ($query) {
		$html = '';
		if (mysql_num_rows($query)>0) {
			$html .= '<ul>';
			while ($link = mysql_fetch_object($query)) {
				$html .= '<li><b><a href="'.$link->url.'" target="_blank">';
				if ($link->title) $html .= $link->title;
				$html .= '</a></b><br />';
				if ($link->description) $html .= SimpleMarkup_HTML($link->description).PHP_EOL;
				// options
				if ($this->Creator->checkUserPermission('edit')) {
					$html .= getDateUserStamp_HTML (DBI_STAMP_CREATED,$link->date_created,$link->user_created);
					$html .= getDateUserStamp_HTML (DBI_STAMP_MODIFIED,$link->date_modified,$link->user_modified);
					$html .= createSmallButton (DBI_EDIT,'gga_edit_link.php?link_id='.$link->link_id,'icon edit');
					$html .= createSmallButton (DBI_REMOVE,'gga_remove_link.php?link_id='.$link->link_id,'icon remove');
				}
				$html .= '</li>';
			}
			$html .= '</ul>';
			if ($this->Creator->checkUserPermission('edit')) {
				$this->Creator->addOption (GGA_ADD_LINK,'gga_edit_link.php','icon addLink');
			}
		}
		else {
			$html .= DBI_NO_RESULTS;
		}
		return $html;
	}
	
}

?>
