<?php

class View_List_NMV_Experiment_Classification_Table extends View_List {

	// CONSTRUCTOR ---------------------------------------------------------------

	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_List_NMV_Experiment_Classification_Table ($args[0]);
		}
	}

	// VIEW ----------------------------------------------------------------------

	public function get_HTML ($results) {
	    global $dbi;
		$this->addSortOption ('english','english','ASC','DESC');
		$this->addSortOption ('deutsch','deutsch','ASC','DESC');
		$this->addSortOption ('ID', 'ID_exp_classification', 'ASC', 'DESC');


		$html = '';
		$html .= $this->getSortOptions_HTML ();

        $options = '';
        if ($dbi->checkUserPermission('admin')) {
        		$options .= createSmallButton(L_EDIT,'nmv_edit_experiment_classification?ID_exp_classification={ID_exp_classification}','icon edit');
        }

		if ($results->num_rows>0) {
		    $html .= '<table class="grid">';
		    $html .= '<th>english</th><th>deutsch</th><th>ID</th><th>Options</th>';
			while ($item = $results->fetch_object()) {
			    $html .= '<tr>
			        <td>' . htmlentities($item->english, ENT_HTML5) . '</td>
			        <td>' . htmlentities($item->deutsch, ENT_HTML5) . '</td>
              <td>' . htmlentities($item->ID_exp_classification, ENT_HTML5) . '</td>
			        <td>' . str_replace('{ID_exp_classification}', $item->ID_exp_classification, $options) . '</td>
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
