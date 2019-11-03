<?php

class View_List_NMV_Perpetrators extends View_List {
	
	// CONSTRUCTOR ---------------------------------------------------------------
	
	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_List_NMV_Perpetrators ($args[0]);
		}
	}
	
	// VIEW ----------------------------------------------------------------------	
	
	public function get_HTML ($results) {
		$html = '';
		$html .= $this->getBrowseOptions_HTML ();
		if ($results->num_rows>0) {
			$html .= '<ul>';
			while ($item = $results->fetch_object()) {
				$html .= '<li><a href="nmv_view_perpetrator?ID_perpetrator='.$item->ID_perpetrator.'">'
				    . ($item->surname != '' ? $item->surname : '?')
				    . ' ('.$item->ID_perpetrator.')'
				    . '</a></li>';
				// options
				//if ($this->Creator->checkUserPermission('edit')) {
					//$html .= getDateUserStamp_HTML (L_STAMP_CREATED,$item->date_created,$abbreviation->user_created);
					//$html .= getDateUserStamp_HTML (L_STAMP_MODIFIED,$item->date_modified,$abbreviation->user_modified);
					//$html .= createSmallButton (L_EDIT,'nmv_edit_victim.php?id='.$item->ID_victim,'icon edit');
					//$html .= createSmallButton (L_REMOVE,'nmv_remove_victim.php?id='.$item->ID_victim,'icon remove');
				//}
			}
			$html .= '</ul>';
		}
		else {
			$html .= L_NO_RESULTS;
		}
		return $html;
	}
	
}

?>
