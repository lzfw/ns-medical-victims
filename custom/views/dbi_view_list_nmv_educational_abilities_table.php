<?php

class View_List_NMV_Educational_Abilities_Table extends View_List {

	// CONSTRUCTOR ---------------------------------------------------------------

	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new View_List_NMV_Educational_Abilities_Table ($args[0]);
		}
	}

	// VIEW ----------------------------------------------------------------------

	public function get_HTML ($results) {
	    global $dbi;
		$this->addSortOption ('english','english','ASC','DESC');
		$this->addSortOption ('deutsch','deutsch','ASC','DESC');
		$this->addSortOption ('ID', 'ID_educational_abilities', 'ASC', 'DESC');


		$html = '';
		$html .= $this->getBrowseOptions_HTML ();
		$html .= $this->getSortOptions_HTML ();

        $options = '';
        if ($dbi->checkUserPermission('admin')) {
        		$options .= createSmallButton(L_EDIT,'nmv_edit_educational_abilities?ID_educational_abilities={ID_educational_abilities}','icon edit');
        }

		if ($results->num_rows>0) {
		    $html .= '<table class="grid">';
		    $html .= '<th>english</th><th>deutsch</th><th>ID</th><th>Options</th>';
			while ($item = $results->fetch_object()) {
			    $html .= '<tr>
			        <td>' . htmlentities($item->english, ENT_HTML5) . '</td>
			        <td>' . htmlentities($item->deutsch, ENT_HTML5) . '</td>
              <td>' . htmlentities($item->ID_educational_abilities, ENT_HTML5) . '</td>
			        <td>' . str_replace('{ID_educational_abilities}', $item->ID_educational_abilities, $options) . '</td>
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
