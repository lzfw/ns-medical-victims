<?php

class View_List_Table_NMV_Victims_Exp extends View_List {

	// CONSTRUCTOR ---------------------------------------------------------------

	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_List_Table_NMV_Victims_Exp ($args[0]);
		}
	}

	// VIEW ----------------------------------------------------------------------

	public function get_HTML ($results) {
	    global $dbi;
		$this->addSortOption ('ID','ID_victim','ASC','DESC');
		$this->addSortOption ('Surname','surname','ASC','DESC');
		$this->addSortOption ('First Name','first_names','ASC','DESC');
		$this->addSortOption ('Born','birth_year','ASC','DESC');
		$this->addSortOption ('Birth Country','birth_country','ASC','DESC');
		$this->addSortOption ('Nationality (1938)','nationality_1938','ASC','DESC');
		$this->addSortOption ('Ethnic Group','ethnic_group','ASC','DESC');
		$this->addSortOption ('Survival','survival','ASC','DESC');
		$html = '';
		//$html .= $this->getBrowseOptions_HTML ();
		$html .= $this->getSortOptions_HTML ();

		if ($results->num_rows>0) {
		    $html .= '<table class="grid">';
		    $html .= '<th>Surname</th><th>First Names</th><th>ID</th><th>Born</th><th>Birth Country</th><th>Nationality (1938)</th><th>Ethnic Group</th><th>Survival</th><th>Options</th>';
			// buttons view, edit, delete connection victim-experiment
			while ($item = $results->fetch_object()) {
					$options = '';
	        $options .= createSmallButton('View Victim-Research',"nmv_view_victim_experiment?ID_vict_exp=$item->ID_vict_exp",'icon view');
	        if ($dbi->checkUserPermission('edit')) {
	        		$options .= createSmallButton(L_EDIT,"nmv_edit_victim_experiment?ID_vict_exp=$item->ID_vict_exp",'icon edit');	        }
	        if ($dbi->checkUserPermission('admin')) {
	        		$options .= createSmallButton(L_DELETE,"nmv_remove_victim_experiment?ID_vict_exp=$item->ID_vict_exp",'icon delete');
	        }

			    $html .= '<tr>
			        <td><a href="nmv_view_victim?ID_victim=' . $item->ID_victim . '">' . htmlentities($item->surname, ENT_HTML5) . '</a></td>
							<td>' . htmlentities($item->first_names, ENT_HTML5) . '</td>
			        <td><a href="nmv_view_victim?ID_victim=' . $item->ID_victim . '">' . $item->ID_victim . '</a></td>
							<td>' . htmlentities($item->birth_year, ENT_HTML5) . '</td>
							<td>' . htmlentities($item->birth_country, ENT_HTML5) . '</td>
							<td>' . htmlentities($item->nationality_1938, ENT_HTML5) . '</td>
							<td>' . htmlentities($item->ethnic_group, ENT_HTML5) . '</td>
							<td>' . htmlentities($item->survival, ENT_HTML5) . '</td>
			        <td>' . str_replace('{ID_victim}', $item->ID_victim, $options) . '</td>
			    </tr>';
			}
			$html .= '</table>';
			$html .= $this->getBrowseOptions_HTML ();
		}
		else {
			$html .= L_NO_RESULTS;
		}
		return $html;
	}
}
