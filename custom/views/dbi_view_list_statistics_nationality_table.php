<?php

class View_List_Statistics_Nationality_Table extends View_List {

	// CONSTRUCTOR ---------------------------------------------------------------

	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_List_Statistics_Nationality_Table ($args[0]);
			case 2: return new View_List_Statistics_Nationality_Table ($args[0], $args[1]);
		}
	}

	// VIEW ----------------------------------------------------------------------

	public function get_HTML ($results) {
	  global $dbi;
		$this->addSortOption ('Nationality', 'nationality', 'ASC', 'DESC');
		$this->addSortOption ('Number','anumber','ASC','DESC');
		$this->addSortOption ('Number','bnumber','ASC','DESC');
		$html = '';

		$html .= $this->getSortOptions_HTML ();


		if ($results->num_rows>0) {
            if (($dbi->checkUserPermission('mpg'))) {
                $html .= '<table class="grid">';
                $html .= '<th>Nationality (1938)</th><th>mpg project</th>';
                $asum = 0;
                $bsum = 0;
                while ($item = $results->fetch_object()) {
                    $nationality = $item->nationality == '' ? 'NO ENTRY' : htmlentities((string) $item->nationality, ENT_HTML5);
                    $html .= '<tr>
							<td>' . $nationality . '</td>
			    		<td>' . htmlentities((string) $item->bnumber, ENT_HTML5) . '</td>
			    </tr>';
                    $asum += $item->anumber;
                    $bsum += $item->bnumber;
                }
                $html .= '	<tr>
										<td><strong>total number of victims</strong></td>
										<td><strong>' . $bsum . '</strong></td>
									</tr>
								</table>';
            } else {
                $html .= '<table class="grid">';
                $html .= '<th>Nationality (1938)</th><th>all victims</th><th>mpg project</th>';
                $asum = 0;
                $bsum = 0;
                while ($item = $results->fetch_object()) {
                    $nationality = $item->nationality == '' ? 'NO ENTRY' : htmlentities((string) $item->nationality, ENT_HTML5);
                    $html .= '<tr>
							<td>' . $nationality . '</td>
			    		<td>' . htmlentities((string) $item->anumber, ENT_HTML5) . '</td>
			    		<td>' . htmlentities((string) $item->bnumber, ENT_HTML5) . '</td>
			    </tr>';
                    $asum += $item->anumber;
                    $bsum += $item->bnumber;
                }
                $html .= '	<tr>
										<td><strong>total number of victims</strong></td>
										<td><strong>' . $asum . '</strong></td>
										<td><strong>' . $bsum . '</strong></td>
									</tr>
								</table>';
            }
		}
		else {
			$html .= L_NO_RESULTS;
		}
		return $html;
	}
}
