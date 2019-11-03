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
		$this->addSortOption ('Title','experiment_title','ASC','DESC');
		$this->addSortOption ('Field of Interest','field_of_interest','ASC, experiment_title ASC','DESC, experiment_title DESC');
		$this->addSortOption ('ID','ID_experiment','ASC','DESC');
		$this->addSortOption ('Classification','classification','ASC, experiment_title ASC','DESC, experiment_title DESC');
		$html = '';
		$html .= $this->getBrowseOptions_HTML ();
		$html .= $this->getSortOptions_HTML ();

        $options = '';
        if ($dbi->checkUserPermission('edit')) {
        		$options .= createSmallButton(L_EDIT,'nmv_edit_experiment?ID_experiment={ID_experiment}','icon edit');
        }
        if ($dbi->checkUserPermission('admin')) {
        		$options .= createSmallButton(L_DELETE,'nmv_remove_experiment?ID_experiment={ID_experiment}','icon delete');
        }

		if ($results->num_rows>0) {
		    $html .= '<table class="grid">';
		    $html .= '<th>Title</th><th>Field of Interest</th><th>ID</th><th>Classification</th><th>Options</th>';
			while ($item = $results->fetch_object()) {
			    $html .= '<tr>
			        <td><a href="nmv_view_experiment?ID_experiment=' . $item->ID_experiment . '">' . htmlentities($item->experiment_title, ENT_HTML5) . '</a></td>
			        <td>' . htmlentities($item->field_of_interest, ENT_HTML5) . '</td>
			        <td>' . $item->ID_experiment . '</td>
			        <td>' . htmlentities($item->classification, ENT_HTML5) . '</td>
			        <td>' . str_replace('{ID_experiment}', $item->ID_experiment, $options) . '</td>
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
