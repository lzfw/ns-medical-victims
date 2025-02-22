<?php

class View_List_NMV_Institution_Order_Table extends View_List {

	// CONSTRUCTOR ---------------------------------------------------------------

	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_List_NMV_Institution_Order_Table ($args[0]);
		}
	}

	// VIEW ----------------------------------------------------------------------

	public function get_HTML ($results) {
	    global $dbi;
		$this->addSortOption ('institution order','institution_order','ASC','DESC');
		$this->addSortOption ('ID', 'ID_institution_order', 'ASC', 'DESC');
		$html = '';
		$html .= $this->getSortOptions_HTML ();

        $options = '';
        if ($dbi->checkUserPermission('admin')) {
        		$options .= createSmallButton(L_EDIT,'nmv_edit_institution_order?ID_institution_order={ID_institution_order}','icon edit');
        }

		if ($results->num_rows>0) {
		    $html .= '<table class="grid">';
		    $html .= '<th>institution_order</th><th>ID</th><th>Options</th>';
			while ($item = $results->fetch_object()) {
			    $html .= '<tr>
			        <td>' . htmlentities((string) $item->institution_order, ENT_HTML5) . '</td>
              <td>' . htmlentities((string) $item->ID_institution_order, ENT_HTML5) . '</td>
			        <td>' . str_replace('{ID_institution_order}', $item->ID_institution_order, $options) . '</td>
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
