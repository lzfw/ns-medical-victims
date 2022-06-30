<?php

class View_List_NMV_Diagnosis_Table extends View_List {

	// CONSTRUCTOR ---------------------------------------------------------------

	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_List_NMV_Diagnosis_Table ($args[0]);
		}
	}

	// VIEW ----------------------------------------------------------------------

	public function get_HTML ($results) {
	    global $dbi;
		$this->addSortOption ('diagnosis','diagnosis','ASC','DESC');
		$this->addSortOption ('ID', 'ID_diagnosis', 'ASC', 'DESC');


		$html = '';
		$html .= $this->getSortOptions_HTML ();

        $options = '';
        if ($dbi->checkUserPermission('admin')) {
        		$options .= createSmallButton(L_EDIT,'nmv_edit_diagnosis?ID_diagnosis={ID_diagnosis}','icon edit');
        }

		if ($results->num_rows>0) {
		    $html .= '<table class="grid">';
		    $html .= '<th>diagnosis</th><th>ID</th><th>Options</th>';
			while ($item = $results->fetch_object()) {
			    $html .= '<tr>
			        <td>' . htmlentities($item->diagnosis, ENT_HTML5) . '</td>
              <td>' . htmlentities($item->ID_diagnosis, ENT_HTML5) . '</td>
			        <td>' . str_replace('{ID_diagnosis}', $item->ID_diagnosis, $options) . '</td>
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
