<?php

class View_List_Statistics_Gender_Ethnicgroup_Table extends View_List {

	// CONSTRUCTOR ---------------------------------------------------------------

	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_List_Statistics_Gender_Ethnicgroup_Table ($args[0]);
			case 2: return new View_List_Statistics_Gender_Ethnicgroup_Table ($args[0], $args[1]);
		}
	}

	// VIEW ----------------------------------------------------------------------

	public function get_HTML ($results) {
	  global $dbi;
		$this->addSortOption ('Ethnic Group', 'ethnicgroup', 'ASC', 'DESC');
		$this->addSortOption ('Gender','gender, ethnicgroup','ASC', 'DESC');
		$this->addSortOption ('Number','anumber','ASC','DESC');
		$this->addSortOption ('Number','bnumber','ASC','DESC');
		$html = '';

		$html .= $this->getSortOptions_HTML ();


		if ($results->num_rows>0) {
		    $html .= '<table class="grid">';
		    $html .= '<th>Ethnic Group</th><th>Gender</th><th>all victims</th><th>mpg only</th>';
				$asum = 0;
				$bsum = 0;
			while ($item = $results->fetch_object()) {
				$ethnicgroup = $item->ethnicgroup == '' ? 'No Entry' : htmlentities($item->ethnicgroup, ENT_HTML5);
				$gender = $item->gender == '' ? 'No Entry' : htmlentities($item->gender, ENT_HTML5);
				$html .= '<tr>
							<td>' . $ethnicgroup  . '</td>
							<td>' . $gender  . '</td>
			    		<td>' . htmlentities($item->anumber, ENT_HTML5) . '</td>
			    		<td>' . htmlentities($item->bnumber, ENT_HTML5) . '</td>
			    </tr>';
				$asum += $item->anumber;
				$bsum += $item->bnumber;
			}
			$html .= '	<tr>
										<td><strong>total number of victims</strong></td>
										<td><strong></strong></td>
										<td><strong>' . $asum . '</strong></td>
										<td><strong>' . $bsum . '</strong></td>
									</tr>
								</table>
								';
		}
		else {
			$html .= L_NO_RESULTS;
		}
		return $html;
	}
}
