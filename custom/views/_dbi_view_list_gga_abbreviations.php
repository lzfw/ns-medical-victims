<?php
// last known update: 2013-01-25

class View_List_GGA_Abbreviations extends View_List {
	
	// CONSTRUCTOR ---------------------------------------------------------------
	
	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_List_GGA_Abbreviations ($args[0]);
		}
	}
	
	// VIEW ----------------------------------------------------------------------	
	
	public function get_HTML ($results) {
		$html = '';
		if (mysql_num_rows($results)>0) {
			$html .= '<ul class="icons abbreviation">';
			while ($abbreviation = mysql_fetch_object($results)) {
				$html .= '<li><b>';
				if ($abbreviation->name) $html .= $abbreviation->name;
				$html .= '</b><br />';
				if ($abbreviation->description) $html .= $abbreviation->description;
				$html .= '<br />';
				// options
				if ($this->Creator->checkUserPermission('edit')) {
					$html .= getDateUserStamp_HTML (DBI_STAMP_CREATED,$abbreviation->date_created,$abbreviation->user_created);
					$html .= getDateUserStamp_HTML (DBI_STAMP_MODIFIED,$abbreviation->date_modified,$abbreviation->user_modified);
					$html .= createSmallButton (DBI_EDIT,'gga_edit_abbreviation.php?abbreviation_id='.$abbreviation->abbreviation_id,'icon edit');
					$html .= createSmallButton (DBI_REMOVE,'gga_remove_abbreviation.php?abbreviation_id='.$abbreviation->abbreviation_id,'icon remove');
				}
				$html .= '</li>';
			}
			$html .= '</ul>';
			if ($this->Creator->checkUserPermission('edit')) {
				$this->Creator->addOption (GGA_ADD_ABBREVIATION,'gga_edit_abbreviation.php','icon addAbbreviation');
			}
		}
		else {
			$html .= DBI_NO_RESULTS;
		}
		return $html;
	}
	
}

?>
