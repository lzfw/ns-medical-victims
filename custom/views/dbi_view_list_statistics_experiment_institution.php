<?php

class View_List_Statistics_Experiment_Institution extends View_List {

	// CONSTRUCTOR ---------------------------------------------------------------

	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_List_Statistics_Experiment_Institution ($args[0]);
			case 2: return new View_List_Statistics_Experiment_Institution ($args[0], $args[1]);
		}
	}

	// VIEW ----------------------------------------------------------------------

	public function get_HTML ($results) {
	  global $dbi;
		$this->addSortOption ('Institution','institution','ASC', 'DESC');
		$this->addSortOption ('Number','anumber','ASC','DESC');
		$this->addSortOption ('Number','bnumber','ASC','DESC');
		$html = '';

		$html .= $this->getSortOptions_HTML ();


		if ($results->num_rows>0) {
		    $html .= '<table class="grid">';
		    $html .= '<th>Institution</th><th>complete database</th><!--<th>mpg only</th>-->';
				$asum = 0;
				$bsum = 0;
			while ($item = $results->fetch_object()) {
					$institution = $item->institution == '' ? 'No entry' : htmlentities($item->institution, ENT_HTML5);
					$institution .= $item->institution == '' ? '' : ' (ID ' . htmlentities($item->ID_institution . ')', ENT_HTML5);
			    $html .= '<tr>
			        <td><a href="nmv_view_institution?ID_institution=' . $item->ID_institution . '"> ' . $institution . '</a></td>
			        <td>' . htmlentities($item->anumber, ENT_HTML5) . '</td>
    		       <!-- <td>' . htmlentities($item->bnumber, ENT_HTML5) . '</td>-->
			    </tr>';
					$asum += $item->anumber;
					$bsum += $item->bnumber;
			}
			$html .= '	<tr>
										<td><strong>total number of victim-experiment-connections</strong></td>
										<td><strong>' . $asum . '</strong></td>
										<!--<td><strong>' . $bsum . '</strong></td>-->
									</tr>
								</table>';
		}
		else {
			$html .= L_NO_RESULTS;
		}
		return $html;
	}
}
