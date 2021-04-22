<?php
/**
 * creates a Buttons to sort a view table by a certain column
 */


class SortOption {

	// CONSTRUCTORS --------------------------------------------------------------

	protected function __construct ($title,$field_name,$default_order,$alternative_order) {
		// addSortOption ( title , field_name , default_order , alternative_order )
		$this->title = $title;
		$this->field_name = $field_name;
		$this->default_order = $default_order;
		$this->alternative_order = $alternative_order;
	}

	static public function create () {
		// create ( creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 4: return new SortOption ($args[0],$args[1],$args[2],$args[3]);
		}
	}

	// OUTPUT --------------------------------------------------------------------

	function getHTML ($dbi) {
		$html = '';
		if ($this->field_name == $dbi->getUserVar('sort') && $this->default_order == $dbi->getUserVar('order')) {
			$html .= "<a href=\"?{$dbi->getUserVar('querystring')}&skip={$dbi->getUserVar('skip')}&sort=$this->field_name&order=$this->alternative_order\">";
		}
		else {
			$html .= "<a href=\"?{$dbi->getUserVar('querystring')}&skip={$dbi->getUserVar('skip')}&sort=$this->field_name&order=$this->default_order\">";
		}
		$html .= '<span';
		if ($this->field_name == $dbi->getUserVar('sort')) {
			if ($dbi->getUserVar('order') == $this->default_order) {
				$html .= ' class="icon sortDesc"';
			}
			if ($dbi->getUserVar('order') == $this->alternative_order) {
				$html .= ' class="icon sortAsc"';
			}
		}
		$html .= '>';
		$html .= $this->title;
		$html .= '</span>';
		$html .= '</a>'.PHP_EOL;
		return $html;
	}
}

?>
