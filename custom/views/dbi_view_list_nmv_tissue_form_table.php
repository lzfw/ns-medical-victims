<?php

class View_List_NMV_Tissue_Form_Table extends View_List {

	// CONSTRUCTOR ---------------------------------------------------------------

	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_List_NMV_Tissue_Form_Table ($args[0]);
		}
	}

	// VIEW ----------------------------------------------------------------------

	public function get_HTML ($results) {
	    global $dbi;
		$this->addSortOption ('tissue form','tissue_form','ASC','DESC');
		$this->addSortOption ('ID', 'ID_tissue_form', 'ASC', 'DESC');


		$html = '';
		$html .= $this->getSortOptions_HTML ();

        $options = '';
        if ($dbi->checkUserPermission('admin')) {
        		$options .= createSmallButton(L_EDIT,'nmv_edit_tissue_form?ID_tissue_form={ID_tissue_form}','icon edit');
        }

		if ($results->num_rows>0) {
		    $html .= '<table class="grid">';
		    $html .= '<th>tissue form</th><th>ID</th><th>Options</th>';
			while ($item = $results->fetch_object()) {
			    $html .= '<tr>
			        <td>' . htmlentities((string) $item->tissue_form, ENT_HTML5) . '</td>
              <td>' . htmlentities((string) $item->ID_tissue_form, ENT_HTML5) . '</td>
			        <td>' . str_replace('{ID_tissue_form}', $item->ID_tissue_form, $options) . '</td>
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
