<?php

class View_List_NMV_Victims_Table extends View_List {

	// CONSTRUCTOR ---------------------------------------------------------------

	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_List_NMV_Victims_Table ($args[0]);
		}
	}

	// VIEW ----------------------------------------------------------------------

	public function get_HTML ($results, $type = 'victim') {
	    global $dbi;

		// toggle columns
		$column = ['Birth Country', 'Death Year', 'Death Place', 'Death Country', 'Cause of Death',
							'Twin', 'Gender', 'Family Status', 'Religion', 'Nationality (1938)', 'Ascribed Ethnic Group',
							'Education', 'Occupation', 'Occupation Details', 'Arrest Location', 'Arrest County',
							'Status Evaluation', 'MPG-Project'
						];
		// browse and order results
		$this->addSortOption ('Name','surname','ASC, first_names ASC','DESC, first_names DESC');
		$this->addSortOption ('ID','ID_victim','ASC','DESC');
		$this->addSortOption ('Birth Year', 'birth_year', 'ASC', 'DESC');
		$this->addSortOption ('Birth Country', 'birth_country', 'ASC', 'DESC');
		$this->addSortOption ('Death Year', 'death_year', 'ASC', 'DESC');
		$this->addSortOption ('Death Country', 'death_country', 'ASC', 'DESC');
		// foreach ($column as $key=>$value) :
		// 	$this->addSortOption ($value, $key, 'ASC', 'DESC');
		// endforeach;

		$html = '';
		$html .= $this->getBrowseOptions_HTML ();
		$html .= $this->getSortOptions_HTML ();

		// hide / show columns
		// TODO in classe packen (wie sort)
		$html .= '<br><p><strong>Show more columns: </strong></p>';
		$i = 0;
		foreach ($column as $value):
				$html .= '<input class="toggle_box toggle_box_'.$i.'" id="toggle_'.$i.'" type="checkbox">
									<label class="toggle_label" for="toggle_'.$i.'">'.$value.'</label>';
				if (($i+1) % 4 == 0 ):
					$html .= '<br>';
				endif;
				$i++;
		endforeach;
		$html .= '<br><br> ';

		// create buttons for action on entriey
    $options = '';
    $options .= createSmallButton($type == 'victim' ? 'View Victim' : 'View Prisoner Assistant', 'nmv_view_victim?ID_victim={ID_victim}', 'icon view');

    if ($dbi->checkUserPermission('edit')) {
    		$options .= createSmallButton(L_EDIT, $type == 'victim' ? 'nmv_edit_victim?ID_victim={ID_victim}' : 'nmv_edit_victim?ID_victim={ID_victim}&type=prisoner_assistant', 'icon edit');
    }
    if ($dbi->checkUserPermission('admin')) {
    		$options .= createSmallButton(L_DELETE,'nmv_remove_victim?ID_victim={ID_victim}','icon delete');
    }

		// create table
		if ($results->num_rows>0) {
		    $html .= '<table class="grid">';
				//table header
		    $html .= '<th>ID</th>
									<th>Surname</th>
									<th>First Names</th>
									<th>Birth Year</th>
									<th>Birth Place</th>';
				$j = 0;
				foreach ($column as $value):
					$html .= '<th class="toggle_element_'.$j.'">'.$value.'</th>';
					$j++;
				endforeach;
				$html .= '<th>Options</th>';
				//table rows
			while ($item = $results->fetch_object()) {
			    $html .= '<tr>
							<td><a href="nmv_view_victim?ID_victim=' . $item->ID_victim . '">' . $item->ID_victim . '</a></td>
			        <td><a href="nmv_view_victim?ID_victim=' . $item->ID_victim . '">' . htmlentities((string) $item->surname, ENT_HTML5) . '</a></td>
			        <td>' . htmlentities((string) $item->first_names, ENT_HTML5) . '</td>
			        <td>' . htmlentities((string) $item->birth_year, ENT_HTML5) . '</td>
			        <td>' . htmlentities((string) $item->birth_place, ENT_HTML5) . '</td>
							<td class="toggle_element_0">' . htmlentities((string) $item->birth_country, ENT_HTML5) . '</td>
							<td class="toggle_element_1">' . htmlentities((string) $item->death_year, ENT_HTML5) . '</td>
							<td class="toggle_element_2">' . htmlentities((string) $item->death_place, ENT_HTML5) . '</td>
							<td class="toggle_element_3">' . htmlentities((string) $item->death_country, ENT_HTML5) . '</td>
							<td class="toggle_element_4">' . htmlentities((string) $item->cause_of_death, ENT_HTML5) . '</td>';
							if($item->twin == -1) :
								 $html .= '<td class="toggle_element_5">yes</td>';
							else :
								$html .= '<td class="toggle_element_5">-</td>';
							endif;
					$html .= '
							<td class="toggle_element_6">' . htmlentities((string) $item->gender, ENT_HTML5) . '</td>
							<td class="toggle_element_7">' . htmlentities((string) $item->marital_family_status, ENT_HTML5) . '</td>
							<td class="toggle_element_8">' . htmlentities((string) $item->religion, ENT_HTML5) . '</td>
							<td class="toggle_element_9">' . htmlentities((string) $item->nationality_1938, ENT_HTML5) . '</td>
							<td class="toggle_element_10">' . htmlentities((string) $item->ethnic_group, ENT_HTML5) . '</td>
							<td class="toggle_element_11">' . htmlentities((string) $item->education, ENT_HTML5) . '</td>
							<td class="toggle_element_12">' . htmlentities((string) $item->occupation, ENT_HTML5) . '</td>
							<td class="toggle_element_13">' . htmlentities((string) $item->occupation_details, ENT_HTML5) . '</td>
							<td class="toggle_element_14">' . htmlentities((string) $item->arrest_location, ENT_HTML5) . '</td>
							<td class="toggle_element_15">' . htmlentities((string) $item->arrest_country, ENT_HTML5) . '</td>
							<td class="toggle_element_16">' . htmlentities((string) $item->evaluation_status, ENT_HTML5) . '</td>';
							if($item->mpg_project == -1) :
								 $html .= '<td class="toggle_element_17">yes</td>';
							else :
								$html .= '<td class="toggle_element_17">-</td>';
							endif;
					$html .= '
			        <td class="nowrap">' . str_replace('{ID_victim}', $item->ID_victim, $options) . '</td>
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
