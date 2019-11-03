<?php

class View_List_Z_Log_Table extends View_List {
	
	// CONSTRUCTOR ---------------------------------------------------------------
	
	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_List_Z_Log_Table ($args[0]);
		}
	}
	
	// VIEW ----------------------------------------------------------------------	
	
	public function get_HTML ($results) {
	    global $dbi;
		$this->addSortOption (L_DATE_TIME,'datetime','ASC','DESC');
		$this->addSortOption (L_OPERATION,'operation','ASC','DESC');
		$this->addSortOption (L_ENTITY,'entity','ASC','DESC');
		$html = '';
		$html .= $this->getBrowseOptions_HTML ();
		$html .= $this->getSortOptions_HTML ();

		if ($results->num_rows>0) {
		    $html .= '<table class="grid">';
		    $html .= '<th>'.L_DATE_TIME.' (UTC)</th><th>'.L_OPERATION.'</th><th>'.L_ENTITY.'</th><th>'.L_RESULT.'</th><th>'.L_ROW_ID.'</th><th>'.L_DETAILS.'</th>';
			while ($item = $results->fetch_object()) {
			    $html .= '<tr>
			        <td>' . htmlentities($item->datetime, ENT_HTML5) . '</td>
			        <td>' . htmlentities($item->operation, ENT_HTML5) . '</td>
			        <td>' . htmlentities($item->entity, ENT_HTML5) . '</td>
			        <td>' . htmlentities($item->result, ENT_HTML5) . '</td>
			        <td>' . htmlentities($item->row_id, ENT_HTML5) . '</td>
			        <td>' . htmlentities($item->details, ENT_HTML5) . '</td>
			    </tr>';
			}
			$html .= '</table>';
		}
		else {
			$html .= L_NO_RESULTS;
		}
		return $html;
	}
}
