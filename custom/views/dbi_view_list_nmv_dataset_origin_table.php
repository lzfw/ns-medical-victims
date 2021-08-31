<?php

class View_List_NMV_Dataset_Origin_Table extends View_List {

	// CONSTRUCTOR ---------------------------------------------------------------

	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_List_NMV_Dataset_Origin_Table ($args[0]);
		}
	}

	// VIEW ----------------------------------------------------------------------

	public function get_HTML ($results) {
	    global $dbi;
		$this->addSortOption ('work group','work_group','ASC','DESC');
		$this->addSortOption ('ID', 'ID_dataset_origin', 'ASC', 'DESC');


		$html = '';
		$html .= $this->getSortOptions_HTML ();

        $options = '';
        if ($dbi->checkUserPermission('admin')) {
        		$options .= createSmallButton(L_EDIT,'nmv_edit_dataset_origin?ID_dataset_origin={ID_dataset_origin}','icon edit');
        }

		if ($results->num_rows>0) {
		    $html .= '<table class="grid">';
		    $html .= '<th>work group</th><th>ID</th><th>Options</th>';
			while ($item = $results->fetch_object()) {
			    $html .= '<tr>
			        <td>' . htmlentities($item->work_group, ENT_HTML5) . '</td>
              <td>' . htmlentities($item->ID_dataset_origin, ENT_HTML5) . '</td>
			        <td>' . str_replace('{ID_dataset_origin}', $item->ID_dataset_origin, $options) . '</td>
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
