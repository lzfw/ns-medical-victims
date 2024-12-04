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
						. ' '
						. ($item->first_names != '' ? $item->first_names : '?')
				    . ' ('.$item->ID_perpetrator.')'
				    . '</a></li>';
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
