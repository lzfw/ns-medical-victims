<?php

class View_List_Table_NMV_Victims_Details extends View_List {

	// CONSTRUCTOR ---------------------------------------------------------------

	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_List_Table_NMV_Victims_Details ($args[0]);
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
		$this->addSortOption ('Ascribed Ethnic Group','ethnic_group','ASC','DESC');
		$html = '';
		$html .= $this->getSortOptions_HTML ();

		if ($results->num_rows>0) {
		    $html .= '<table class="grid">';
		    $html .= '<th>Surname</th><th>First Names</th><th>ID</th><th>Born</th><th>Birth Country</th><th>Nationality (1938)</th><th>Ascribed Ethnic Group</th><th>Options</th>';
			// buttons view, edit, delete connection victim-experiment
			while ($item = $results->fetch_object()) {
					$options = '';
	        $options .= createSmallButton('View Victim',"nmv_view_victim?ID_victim=$item->ID_victim",'icon view');
	        if ($dbi->checkUserPermission('edit')) {
	        		$options .= createSmallButton(L_EDIT,"nmv_edit_victim?ID_victim=$item->ID_victim",'icon edit');	        }
	        if ($dbi->checkUserPermission('admin')) {
	        		$options .= createSmallButton(L_DELETE,"nmv_remove_victim?ID_victim=$item->ID_victim",'icon delete');
	        }

			    $html .= '<tr>
			        <td><a href="nmv_view_victim?ID_victim=' . $item->ID_victim . '">' . htmlentities((string) $item->surname, ENT_HTML5) . '</a></td>
							<td>' . htmlentities((string) $item->first_names, ENT_HTML5) . '</td>
			        <td><a href="nmv_view_victim?ID_victim=' . $item->ID_victim . '">' . $item->ID_victim . '</a></td>
							<td>' . htmlentities((string) $item->birth_year, ENT_HTML5) . '</td>
							<td class="nowrap">' . htmlentities((string) $item->birth_country, ENT_HTML5) . '</td>
							<td>' . htmlentities((string) $item->nationality_1938, ENT_HTML5) . '</td>
							<td>' . htmlentities((string) $item->ethnic_group, ENT_HTML5) . '</td>
			        <td class="nowrap">' . str_replace('{ID_victim}', $item->ID_victim, $options) . '</td>
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
