<?php

class View_List_NMV_Marital_Family_status_Table extends View_List {

	// CONSTRUCTOR ---------------------------------------------------------------

	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_List_NMV_Marital_Family_status_Table ($args[0]);
		}
	}

	// VIEW ----------------------------------------------------------------------

	public function get_HTML ($results) {
	    global $dbi;
		$this->addSortOption ('english','english','ASC','DESC');
		$this->addSortOption ('ID', 'ID_marital_family_status', 'ASC', 'DESC');


		$html = '';
		$html .= $this->getSortOptions_HTML ();

        $options = '';
        if ($dbi->checkUserPermission('admin')) {
        		$options .= createSmallButton(L_EDIT,'nmv_edit_marital_family_status?ID_marital_family_status={ID_marital_family_status}','icon edit');
        }

		if ($results->num_rows>0) {
		    $html .= '<table class="grid">';
		    $html .= '<th>english</th><th>ID</th><th>Options</th>';
			while ($item = $results->fetch_object()) {
			    $html .= '<tr>
			        <td>' . htmlentities($item->english, ENT_HTML5) . '</td>
              <td>' . htmlentities($item->ID_marital_family_status, ENT_HTML5) . '</td>
			        <td>' . str_replace('{ID_marital_family_status}', $item->ID_marital_family_status, $options) . '</td>
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
