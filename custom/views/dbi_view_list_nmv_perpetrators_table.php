<?php

class View_List_NMV_Perpetrators_Table extends View_List {
	
	// CONSTRUCTOR ---------------------------------------------------------------
	
	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_List_NMV_Perpetrators_Table ($args[0]);
		}
	}
	
	// VIEW ----------------------------------------------------------------------	
	
	public function get_HTML ($results) {
	    global $dbi;
		$this->addSortOption ('Name','surname','ASC, first_names ASC','DESC, first_names DESC');
		$this->addSortOption ('ID','ID_perpetrator','ASC','DESC');
		$this->addSortOption ('Birth Place','birth_place','ASC','DESC');
		$html = '';
		$html .= $this->getBrowseOptions_HTML ();
		$html .= $this->getSortOptions_HTML ();

        $options = '';
        
        $options .= createSmallButton('view perpetrator','nmv_view_perpetrator?ID_perpetrator={ID_perpetrator}','icon view');
        if ($dbi->checkUserPermission('edit')) {
        		$options .= createSmallButton(L_EDIT,'nmv_edit_perpetrator?ID_perpetrator={ID_perpetrator}','icon edit');
        }
        if ($dbi->checkUserPermission('admin')) {
        		$options .= createSmallButton(L_DELETE,'nmv_remove_perpetrator?ID_perpetrator={ID_perpetrator}','icon delete');
        }

		if ($results->num_rows>0) {
		    $html .= '<table class="grid">';
		    $html .= '<th>Surname</th><th>First Names</th><th>id</th><th>Birth Place</th><th>Options</th>';
			while ($item = $results->fetch_object()) {
			    $html .= '<tr>
			        <td><a href="nmv_view_perpetrator?ID_perpetrator=' . $item->ID_perpetrator . '">' . htmlentities((string) $item->surname, ENT_HTML5) . '</a></td>
			        <td>' . htmlentities((string) $item->first_names, ENT_HTML5) . '</td>
			        <td>' . $item->ID_perpetrator . '</td>
			        <td>' . htmlentities((string) $item->birth_place, ENT_HTML5) . '</td>
			        <td>' . str_replace('{ID_perpetrator}', $item->ID_perpetrator, $options) . '</td>
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
