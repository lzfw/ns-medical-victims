<?php

class View_List_Statistics_Experiment_FOI_Table extends View_List {

	// CONSTRUCTOR ---------------------------------------------------------------

	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_List_Statistics_Experiment_FOI_Table ($args[0]);
			case 2: return new View_List_Statistics_Experiment_FOI_Table ($args[0], $args[1]);
		}
	}

	// VIEW ----------------------------------------------------------------------

	public function get_HTML ($results) {
	  global $dbi;
		$this->addSortOption ('Field of Interest','field_of_interest','ASC', 'DESC');
		$this->addSortOption ('Number','anumber','ASC','DESC');
		$this->addSortOption ('Number','bnumber','ASC','DESC');
		$html = '';

		$html .= $this->getSortOptions_HTML ();


		if ($results->num_rows>0) {
		    $html .= '<table class="grid">';
		    $html .= '<th>Field of Interest</th><th>Experiments with this field of interest-tag</th><th>Number of victims (complete database)</th><th>Number of victims of KWI brain research</th>';
				$asum = 0;
				$bsum = 0;
			while ($item = $results->fetch_object()) {
					$field_of_interest = $item->field_of_interest == '' ? 'No indication of the "field of interest" in the experiment' : htmlentities($item->field_of_interest, ENT_HTML5);
					$experiments = $item->experiments;
					$html .= '<tr>
			        <td>' . $field_of_interest . '</td>
			        <td>' . $experiments . '</td>
			        <td>' . htmlentities($item->anumber, ENT_HTML5) . '</td>
			        <td>' . htmlentities($item->bnumber, ENT_HTML5) . '</td>
			    </tr>';
					$asum += $item->anumber;
					$bsum += $item->bnumber;
			}
			$html .= '	<tr>
										<td><strong>total number of victim-experiment-connections</strong></td>
										<td></td>
										<td><strong>' . $asum . '</strong></td>
										<td><strong>' . $bsum . '</strong></td>
									</tr>
								</table>';
		}
		else {
			$html .= L_NO_RESULTS;
		}
		return $html;
	}
}
