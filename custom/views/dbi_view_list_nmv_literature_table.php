<?php

class View_List_NMV_Literature_Table extends View_List {

	// CONSTRUCTOR ---------------------------------------------------------------

	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_List_NMV_Literature_Table ($args[0]);
		}
	}

	// VIEW ----------------------------------------------------------------------

	public function get_HTML ($results) {
	    global $dbi;
		$this->addSortOption ('ID','ID_literature','ASC','DESC');
		$this->addSortOption ('Authors','authors','ASC','DESC');
		$this->addSortOption ('Title','lit_title','ASC','DESC');
		$this->addSortOption ('Year','lit_year','ASC','DESC');
		$html = '';
		$html .= $this->getSortOptions_HTML ();

        $options = '';
        $options .= createSmallButton('View Details','nmv_view_literature?ID_literature={ID_literature}','icon view');

        if ($dbi->checkUserPermission('edit')) {
        		$options .= createSmallButton(L_EDIT,'nmv_edit_literature?ID_literature={ID_literature}','icon edit');
        }
        if ($dbi->checkUserPermission('admin')) {
        		$options .= createSmallButton(L_DELETE,'nmv_remove_literature?ID_literature={ID_literature}','icon delete');
        }

		if ($results->num_rows>0) {
		    $html .= '<table class="grid">';
		    $html .= '<th>ID</th><th>Authors</th><th>Title</th><th>Year</th><th>Options</th>';
			while ($item = $results->fetch_object()) {
			    $html .= '<tr>
							<td>' . $item->ID_literature . '</td>
			        <td><a href="nmv_view_literature?ID_literature=' . $item->ID_literature . '">' . ($item->authors ? htmlentities((string) $item->authors, ENT_HTML5) : '(empty)') . '</a></td>
							<td>' . htmlentities((string) $item->lit_title, ENT_HTML5) . '</td>
							<td>' . htmlentities((string) $item->lit_year, ENT_HTML5) . '</td>
			        <td class="nowrap">' . str_replace('{ID_literature}', $item->ID_literature, $options) . '</td>
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
