<?php

class View_List_Table_NMV_Vic_Exp extends View_List {

	// CONSTRUCTOR ---------------------------------------------------------------

	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_List_Table_NMV_Vic_Exp ($args[0]);
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
		$this->addSortOption ('Gender', 'gender', 'ASC', 'DESC');
		$this->addSortOption ('Ascribed Ethnic Group','ethnic_group','ASC','DESC');
		$this->addSortOption ('Start Year','exp_start_year','ASC','DESC');
		$this->addSortOption ('Duration','experiment_duration','ASC','DESC');
		$this->addSortOption ('End Year','exp_end_year','ASC','DESC');
		$this->addSortOption ('Title of Experiment','experiment_title','ASC','DESC');
		$this->addSortOption ('Institution','institution_name','ASC','DESC');
		$this->addSortOption ('Location Deatails (Exp)','location_details','ASC','DESC');

		$html = '';
		$html .= $this->getSortOptions_HTML ();

		$html .= '<br><p><strong>Please Note: </strong>There are multiple entries for victims who were forced to participate in more than one experiment. </p>';

		if ($results->num_rows>0) {
		    $html .= '<table class="grid">';
		    $html .= '<th>ID</th><th>Surname</th><th>First Names</th><th>Born</th><th>Birth Country</th>
									<th>Gender</th><th>Ascribed Ethnic Group</th><th>Start Year</th><th>Duration</th><th>End Year</th><th>Title of Experiment</th><th>Institution</th><th>Location Details (Experiment)</th><th>Options</th>';
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
							<td><a href="nmv_view_victim?ID_victim=' . $item->ID_victim . '">' . $item->ID_victim . '</a></td>
			        <td><a href="nmv_view_victim?ID_victim=' . $item->ID_victim . '">' . htmlentities($item->surname, ENT_HTML5) . '</a></td>
							<td>' . htmlentities($item->first_names, ENT_HTML5) . '</td>
							<td>' . htmlentities($item->birth_year, ENT_HTML5) . '</td>
							<td class="nowrap">' . htmlentities($item->birth_country, ENT_HTML5) . '</td>
							<td>' . htmlentities($item->gender, ENT_HTML5) . '</td>
							<td>' . htmlentities($item->ethnic_group, ENT_HTML5) . '</td>
							<td>' . htmlentities($item->exp_start_year, ENT_HTML5) . '</td>
							<td>' . htmlentities($item->experiment_duration, ENT_HTML5) . '</td>
							<td>' . htmlentities($item->exp_end_year, ENT_HTML5) . '</td>
							<td>' . htmlentities($item->experiment_title, ENT_HTML5) . '</td>
							<td class="prewrap">' . htmlentities($item->institution_name, ENT_HTML5) . '</td>
							<td>' . htmlentities($item->location_details, ENT_HTML5) . '</td>
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
