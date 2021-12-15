<?php

class View_List_NMV_Institutions_Table extends View_List {

	// CONSTRUCTOR ---------------------------------------------------------------

	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_List_NMV_Institutions_Table ($args[0]);
		}
	}

	// VIEW ----------------------------------------------------------------------

	public function get_HTML ($results) {
	    global $dbi;
		$this->addSortOption ('Name','institution_name','ASC','DESC');
		$this->addSortOption ('ID', 'ID_institution', 'ASC', 'DESC');
		$this->addSortOption ('Location','location','ASC','DESC');
		$this->addSortOption ('Country','country','ASC, institution_name ASC','DESC, institution_name DESC');
		$this->addSortOption ('Type','itype','ASC, institution_name ASC','DESC, institution_name DESC');
		$html = '';
		$html .= $this->getSortOptions_HTML ();

        $options = '';
        $options .= createSmallButton('View Institution', 'nmv_view_institution?ID_institution={ID_institution}','icon view');
        if ($dbi->checkUserPermission('edit')) {
        		$options .= createSmallButton(L_EDIT,'nmv_edit_institution?ID_institution={ID_institution}','icon edit');
        }
        if ($dbi->checkUserPermission('admin')) {
        		$options .= createSmallButton(L_DELETE,'nmv_remove_institution?ID_institution={ID_institution}','icon delete');
        }

		if ($results->num_rows>0) {
		    $html .= '<table class="grid">';
		    $html .= '<th>Institution</th><th>ID</th><th>Location</th><th>Present Country</th><th>Type</th><th>Notes</th><th>Options</th>';
			while ($item = $results->fetch_object()) {
			    $html .= '<tr>
			        <td><a href="nmv_view_institution?ID_institution=' . $item->ID_institution . '">' . htmlentities($item->institution_name, ENT_HTML5) . '</a></td>
			        <td>' . htmlentities($item->ID_institution, ENT_HTML5) . '</td>
			        <td>' . htmlentities($item->location, ENT_HTML5) . '</td>
			        <td>' . htmlentities($item->country, ENT_HTML5) . '</td>
			        <td>' . htmlentities($item->itype, ENT_HTML5) . '</td>
			        <td>' . htmlentities($item->notes, ENT_HTML5) . '</td>
			        <td class="nowrap">' . str_replace('{ID_institution}', $item->ID_institution, $options) . '</td>
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
