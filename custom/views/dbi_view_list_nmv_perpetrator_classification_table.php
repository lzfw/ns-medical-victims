<?php

class View_List_NMV_Perpetrator_Classification_Table extends View_List {

	// CONSTRUCTOR ---------------------------------------------------------------

	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_List_NMV_Perpetrator_Classification_Table ($args[0]);
		}
	}

	// VIEW ----------------------------------------------------------------------

	public function get_HTML ($results) {
	    global $dbi;
		$this->addSortOption ('classification','classification','ASC','DESC');
		$this->addSortOption ('ID', 'ID_perp_class', 'ASC', 'DESC');


		$html = '';
		$html .= $this->getSortOptions_HTML ();

        $options = '';
        if ($dbi->checkUserPermission('admin')) {
        		$options .= createSmallButton(L_EDIT,'nmv_edit_perpetrator_classification?ID_perp_class={ID_perp_class}','icon edit');
        }

		if ($results->num_rows>0) {
		    $html .= '<table class="grid">';
		    $html .= '<th>classification</th><th>ID</th><th>Options</th>';
			while ($item = $results->fetch_object()) {
			    $html .= '<tr>
			        <td>' . htmlentities((string) $item->classification, ENT_HTML5) . '</td>
              <td>' . htmlentities((string) $item->ID_perp_class, ENT_HTML5) . '</td>
			        <td>' . str_replace('{ID_perp_class}', $item->ID_perp_class, $options) . '</td>
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
