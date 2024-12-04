<?php

class View_List_NMV_Source extends View_List {
	
	// CONSTRUCTOR ---------------------------------------------------------------
	
	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_List_NMV_Source ($args[0]);
		}
	}
	
	// VIEW ----------------------------------------------------------------------	
	
	public function get_HTML ($results) {
		$html = '';
		$html .= $this->getBrowseOptions_HTML ();
		if ($results->num_rows>0) {
			$html .= '<ul>';
			while ($item = $results->fetch_object()) {
				$html .= '<li><a href="nmv_view_source?ID_source='.$item->ID_source.'">'
				    . ($item->source_title != '' ? $item->source_title : '?')
				    . ' ('.$item->signature.') ('.$item->description.')'
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
