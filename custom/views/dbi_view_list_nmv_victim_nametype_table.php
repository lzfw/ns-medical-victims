<?php

class View_List_NMV_Victim_Nametype_Table extends View_List {

	// CONSTRUCTOR ---------------------------------------------------------------

	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_List_NMV_Victim_Nametype_Table ($args[0]);
		}
	}

	// VIEW ----------------------------------------------------------------------

	public function get_HTML ($results) {
	    global $dbi;
		$this->addSortOption ('nametype','nametype','ASC','DESC');
		$this->addSortOption ('ID', 'ID_nametype', 'ASC', 'DESC');


		$html = '';
		$html .= $this->getSortOptions_HTML ();

    $options = '';
    if ($dbi->checkUserPermission('admin')) {
    		$options .= createSmallButton(L_EDIT,'nmv_edit_victim_nametype?ID_nametype={ID_nametype}','icon edit');
    }

		if ($results->num_rows>0) {
		    $html .= '<table class="grid">';
		    $html .= '<th>nametype</th><th>ID</th><th>Options</th>';
			while ($item = $results->fetch_object()) {
			    $html .= '<tr>
			        <td>' . htmlentities((string) $item->nametype, ENT_HTML5) . '</td>
              <td>' . htmlentities((string) $item->ID_nametype, ENT_HTML5) . '</td>
			        <td>' . str_replace('{ID_nametype}', $item->ID_nametype, $options) . '</td>
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
