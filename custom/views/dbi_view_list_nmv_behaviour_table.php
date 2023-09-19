<?php
//TEST
class View_List_NMV_Behaviour_Table extends View_List {

	// CONSTRUCTOR ---------------------------------------------------------------

	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_List_NMV_Behaviour_Table ($args[0]);
		}
	}

	// VIEW ----------------------------------------------------------------------

	public function get_HTML ($results) {
	    global $dbi;
		$this->addSortOption ('behaviour','behaviour','ASC','DESC');
		$this->addSortOption ('ID', 'ID_behaviour', 'ASC', 'DESC');


		$html = '';
		$html .= $this->getSortOptions_HTML ();

        $options = '';
        if ($dbi->checkUserPermission('admin')) {
        		$options .= createSmallButton(L_EDIT,'nmv_edit_behaviour?ID_behaviour={ID_behaviour}','icon edit');
        }

		if ($results->num_rows>0) {
		    $html .= '<table class="grid">';
		    $html .= '<th>behaviour</th><th>ID</th><th>Options</th>';
			while ($item = $results->fetch_object()) {
			    $html .= '<tr>
			        <td>' . htmlentities($item->behaviour, ENT_HTML5) . '</td>
              <td>' . htmlentities($item->ID_behaviour, ENT_HTML5) . '</td>
			        <td>' . str_replace('{ID_behaviour}', $item->ID_behaviour, $options) . '</td>
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
