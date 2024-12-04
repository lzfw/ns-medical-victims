<?php

class View_List_NMV_Victims extends View_List {

	// CONSTRUCTOR ---------------------------------------------------------------

	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_List_NMV_Victims ($args[0]);
		}
	}

	// VIEW ----------------------------------------------------------------------

	public function get_HTML ($results) {
		$html = '';
		$html .= $this->getBrowseOptions_HTML ();
		if ($results->num_rows>0) {
			$html .= '<ul>';
			while ($item = $results->fetch_object()) {
				$html .= '<li><a href="nmv_view_victim?ID_victim='.$item->ID_victim.'">'
				    . ($item->surname != '' ? $item->surname : '?')
						. ' '
						. ($item->first_names != '' ? $item->first_names : '?')
				    . ' (ID '.$item->ID_victim.')'
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
