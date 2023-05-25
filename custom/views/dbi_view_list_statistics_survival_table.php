<?php

class View_List_Statistics_Survival_Table extends View_List {

	// CONSTRUCTOR ---------------------------------------------------------------

	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_List_Statistics_Survival_Table ($args[0]);
			case 2: return new View_List_Statistics_Survival_Table ($args[0], $args[1]);
		}
	}

	// VIEW ----------------------------------------------------------------------

	public function get_HTML ($results) {
	  global $dbi;
		$this->addSortOption ('Survival', 'survival', 'ASC', 'DESC');
		$this->addSortOption ('Number','anumber','ASC','DESC');
		$this->addSortOption ('Number','bnumber','ASC','DESC');
		$html = '';

		$html .= $this->getSortOptions_HTML ();


		if ($results->num_rows>0) {
            if (($dbi->checkUserPermission('mpg'))) {
                $html .= '<table class="grid">';
                $html .= '<th>Survival</th><th>mpg project</th>';
                $bsum = 0;
                while ($item = $results->fetch_object()) {
                    if ($item->survivalID == NULL):
                        $survival = 'NO ENTRY';
                    elseif ($item->survivalID == 3 or $item->survivalID == 9):
                        $survival = 'INVALID ENTRY';
                    else:
                        $survival = htmlentities($item->survival, ENT_HTML5);
                    endif;
                    $html .= '<tr>
							<td>' . $survival . '</td>
			    		 <td>' . htmlentities($item->bnumber, ENT_HTML5) . '</td>
			    </tr>';
                    $bsum += $item->bnumber;
                }
                $html .= '	<tr>
										<td><strong>total number of links</strong></td>
										<td><strong>' . $bsum . '</strong></td>
									</tr>
								</table>';
            } else {
                $html .= '<table class="grid">';
                $html .= '<th>Survival</th><th>all victims</th><th>mpg project</th>';
                $asum = 0;
                $bsum = 0;
                while ($item = $results->fetch_object()) {
                    if ($item->survivalID == NULL):
                        $survival = 'NO ENTRY';
                    elseif ($item->survivalID == 3 or $item->survivalID == 9):
                        $survival = 'INVALID ENTRY';
                    else:
                        $survival = htmlentities($item->survival, ENT_HTML5);
                    endif;
                    $html .= '<tr>
							<td>' . $survival . '</td>
			    		<td>' . htmlentities($item->anumber, ENT_HTML5) . '</td>
			    		 <td>' . htmlentities($item->bnumber, ENT_HTML5) . '</td>
			    </tr>';
                    $asum += $item->anumber;
                    $bsum += $item->bnumber;
                }
                $html .= '	<tr>
										<td><strong>total number of links</strong></td>
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
