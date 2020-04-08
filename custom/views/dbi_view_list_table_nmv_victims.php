<?php

class View_List_Table_NMV_Victims extends View_List {

	// CONSTRUCTOR ---------------------------------------------------------------

	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_List_Table_NMV_Victims ($args[0]);
		}
	}

	// VIEW ----------------------------------------------------------------------

	public function get_HTML ($results) {
	    global $dbi;
		$this->addSortOption ('Name','surname','ASC, first_names ASC','DESC, first_names DESC');
		$this->addSortOption ('ID','ID_victim','ASC','DESC');
		$html = '';
		$html .= $this->getBrowseOptions_HTML ();
		$html .= $this->getSortOptions_HTML ();

        $options = '';
        $options .= createSmallButton('View Victim','nmv_view_victim?ID_victim={ID_victim}','icon view');

        if ($dbi->checkUserPermission('edit')) {
        		$options .= createSmallButton(L_EDIT,'nmv_edit_victim?ID_victim={ID_victim}','icon edit');
        }
        if ($dbi->checkUserPermission('admin')) {
        		$options .= createSmallButton(L_DELETE,'nmv_remove_victim?ID_victim={ID_victim}','icon delete');
        }

		if ($results->num_rows>0) {
		    $html .= '<table class="grid">';
		    $html .= '<th>Main Name</th><th>ID</th><th>Born</th><th>Birth Country</th><th>Options</th>';
			while ($item = $results->fetch_object()) {
			    $html .= '<tr>
			        <td><a href="nmv_view_victim?ID_victim=' . $item->ID_victim . '">'
							 . htmlentities($item->surname, ENT_HTML5)
							 . ' ' . htmlentities($item->first_names, ENT_HTML5) . '</a></td>
			        <td><a href="nmv_view_victim?ID_victim=' . $item->ID_victim . '">' . $item->ID_victim . '</a></td>
							<td>' . htmlentities($item->birth_year, ENT_HTML5) . '</td>
							<td>' . htmlentities($item->birth_country, ENT_HTML5) . '</td>
			        <td>' . str_replace('{ID_victim}', $item->ID_victim, $options) . '</td>
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
