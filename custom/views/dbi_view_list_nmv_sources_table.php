<?php

class View_List_NMV_Sources_Table extends View_List {

	// CONSTRUCTOR ---------------------------------------------------------------

	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_List_NMV_Sources_Table ($args[0]);
		}
	}

	// VIEW ----------------------------------------------------------------------

	public function get_HTML ($results) {
	    global $dbi;
		$this->addSortOption ('ID','ID_source','ASC','DESC');
		$this->addSortOption ('Title','source_title','ASC','DESC');
		$this->addSortOption ('Description','description','ASC','DESC');
		$this->addSortOption ('Signature','signature','ASC','DESC');

		$html = '';
		$html .= $this->getSortOptions_HTML ();

        $options = '';

        $options .= createSmallButton('View','nmv_view_source?ID_source={ID_source}','icon view');
        if ($dbi->checkUserPermission('edit')) {
        		$options .= createSmallButton(L_EDIT,'nmv_edit_source?ID_source={ID_source}','icon edit');
        }
        if ($dbi->checkUserPermission('admin')) {
        		$options .= createSmallButton(L_DELETE,'nmv_remove_source?ID_source={ID_source}','icon delete');
        }

		if ($results->num_rows>0) {
		    $html .= '<table class="grid">';
		    $html .= '<th>ID</th><th>Title</th><th>Description</th><th>Signature</th><th>Options</th>';
			while ($item = $results->fetch_object()) {
			    $html .= '<tr>
							<td>' . $item->ID_source . '</td>
			        <td><a href="nmv_view_source?ID_source=' . $item->ID_source . '">' . htmlentities($item->source_title, ENT_HTML5) . '</a></td>
							<td>' . htmlentities($item->description, ENT_HTML5) . '</td>
							<td>' . htmlentities($item->signature, ENT_HTML5) . '</td>
			        <td class="nowrap">' . str_replace('{ID_source}', $item->ID_source, $options) . '</td>
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
