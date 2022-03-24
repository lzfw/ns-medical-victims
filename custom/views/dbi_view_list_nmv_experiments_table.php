<?php

class View_List_NMV_Experiments_Table extends View_List {

	// CONSTRUCTOR ---------------------------------------------------------------

	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_List_NMV_Experiments_Table ($args[0]);
		}
	}

	// VIEW ----------------------------------------------------------------------

	public function get_HTML ($results) {
	    global $dbi;
		$this->addSortOption ('ID','ID_experiment','ASC','DESC');
		$this->addSortOption ('Title','experiment_title','ASC','DESC');
		$this->addSortOption ('Institution','institution_name','ASC','DESC');
		$this->addSortOption ('Objective','objective','ASC, experiment_title ASC','DESC, experiment_title DESC');
		$this->addSortOption ('Start Year', 'start_year', 'ASC, experiment_title ASC', 'DESC, experiment_title ASC');
		$this->addSortOption ('End Year', 'end_year', 'ASC, experiment_title ASC', 'DESC, experiment_title ASC');
		$html = '';
		$html .= $this->getSortOptions_HTML ();

        $options = '';
				$options .= createSmallButton('View','nmv_view_experiment?ID_experiment={ID_experiment}','icon view');
        if ($dbi->checkUserPermission('edit')) {
        		$options .= createSmallButton(L_EDIT,'nmv_edit_experiment?ID_experiment={ID_experiment}','icon edit');
        }
				if ($dbi->checkUserPermission('admin')) {
        		$options .= createSmallButton(L_DELETE,'nmv_remove_experiment?ID_experiment={ID_experiment}','icon delete');
        }

		if ($results->num_rows>0) {
		    $html .= '<table class="grid">';
		    $html .= '<th>ID</th><th>Title</th><th>Institution</th><th>Fields of Interest</th><th>Objective</th><th>Start Year</th><th>End Year</th><th>Options</th>';
			while ($item = $results->fetch_object()) {
			    $html .= '<tr>
							<td>' . $item->ID_experiment . '</td>
			        <td><a href="nmv_view_experiment?ID_experiment=' . $item->ID_experiment . '">' . htmlentities($item->experiment_title, ENT_HTML5) . '</a></td>
							<td>' . htmlentities($item->institution_name, ENT_HTML5) . '</td>
							<td class= "prewrap">' . htmlentities($item->fields_of_interest, ENT_HTML5) . '</td>
			        <td>' . htmlentities($item->objective, ENT_HTML5) . '</td>
			        <td>' . htmlentities($item->start_year, ENT_HTML5) . '</td>
			        <td>' . htmlentities($item->end_year, ENT_HTML5) . '</td>
			        <td class="nowrap">' . str_replace('{ID_experiment}', $item->ID_experiment, $options) . '</td>
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
