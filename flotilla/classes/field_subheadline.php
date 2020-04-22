<?php

class Field_SubHeadline extends Field {

	public $autowidth = false; // default is true

	// CONSTRUCTORS -----------------------------------------------------------

	protected function __construct (Form $Creator, $name, $default_value = NULL) {
		if (isset($name)) {
			$this->Creator = $Creator;
			$this->name = $name;
			$this->user_value = $default_value;
			$this->Creator->debuglog->Write(DEBUG_INFO,'. new SubHeadline "'.$this->name.'" created');
		}
		else {
			$this->Creator->debuglog->Write(DEBUG_ERROR,'. could not create new SubHeadline - name not specified');
		}
	}

	static public function create() {
		// create ( name [, length [, required [, default_value ]]] )
		$args = func_get_args();
		switch (func_num_args()) {
			case 2: return new Field_SubHeadline ($args[0],$args[1]);
			case 3: return new Field_SubHeadline ($args[0],$args[1],$args[2]);
			default: $this->Creator->debuglog->Write(DEBUG_WARNING,'. could not create new SubHeadline - invalid number of arguments');
		}
	}

	public function HTMLOutput () {
		$output = NULL;
		$output .= "\t\t\t<h4";
		$output .= ' name="'.$this->name.'"';
		$output .= ' id="field-'.$this->name.'"';
		if ($this->language) $output .= ' lang="'.$this->language.'"';
		if ($this->direction) $output .= ' dir="'.$this->direction.'"';
		if ($this->is_not_hidden()) {
			$output .= $this->HTMLTitle();
			$output .= $this->HTMLClass();
			$output .= $this->HTMLStyle();
		}
		$output .= '/>';
		$output .= stripslashes($this->user_value);
		$output .= '</h4>'.PHP_EOL;
		return $output;
	}

} // end class Field_SubHeadline

?>
