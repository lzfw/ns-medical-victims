<?php

class View_List_NMV_Country_Table extends View_List {

	// CONSTRUCTOR ---------------------------------------------------------------

	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_List_NMV_Country_Table ($args[0]);
		}
	}

	// VIEW ----------------------------------------------------------------------

	public function get_HTML ($results) {
	    global $dbi;
		$this->addSortOption ('country','country','ASC','DESC');
		$this->addSortOption ('local name','local_name','ASC','DESC');
		$this->addSortOption ('ID', 'ID_country', 'ASC', 'DESC');


		$html = '';
		$html .= $this->getSortOptions_HTML ();

        $options = '';
        if ($dbi->checkUserPermission('admin')) {
        		$options .= createSmallButton(L_EDIT,'nmv_edit_country?ID_country={ID_country}','icon edit');
        }

		if ($results->num_rows>0) {
		    $html .= '<table class="grid">';
		    $html .= '<th>country</th><th>local name</th><th>ID</th><th>Options</th>';
			while ($item = $results->fetch_object()) {
			    $html .= '<tr>
			        <td>' . htmlentities((string) $item->country, ENT_HTML5) . '</td>
			        <td>' . htmlentities((string) $item->local_name, ENT_HTML5) . '</td>
              <td>' . htmlentities((string) $item->ID_country, ENT_HTML5) . '</td>
			        <td>' . str_replace('{ID_country}', $item->ID_country, $options) . '</td>
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
