<?php

class Field_MultiCheckbox extends Field {

	public $Options = array();

	// CONSTRUCTORS --------------------------------------------------------------

	protected function __construct (Form $Creator, $name, $required, $default_option) {
		if (isset($name)) {
			$this->Creator = $Creator;
			$this->name = $name;
			$this->required = $required;
			$this->user_value = $default_option;
			$this->Creator->debuglog->Write(DEBUG_INFO,'. new Multiple Checkbox Field "'.$this->name.'" created');
		}
		else $this->Creator->debuglog->Write(DEBUG_ERROR,'. could not create new Multiple Checkbox Field - name not specified');
	}

	static public function create() {
		// create ( name [, required [, default_option ]] )
		$args = func_get_args();
		switch (func_num_args()) {
			case 2: return new Field_MultiCheckbox ($args[0],$args[1],NULL,NULL);
			case 3: return new Field_MultiCheckbox ($args[0],$args[1],$args[2],NULL);
			case 4: return new Field_MultiCheckbox ($args[0],$args[1],$args[2],$args[3]);
			default: $this->Creator->debuglog->Write(DEBUG_WARNING,'. could not create new Multiple Checkbox Field - invalid number of arguments');
		}
	}

	// SELECT OPTIONS ------------------------------------------------------------

	public function addOption () {
		// addOption ( name, [ value [, title ]] )
		$args = func_get_args();
		switch (func_num_args()) {
			case 0: $this->Options[] = new MultiCheckbox_Option ($this->name,count($this->Options),NULL); break;
			case 1: $this->Options[] = new MultiCheckbox_Option ($this->name,$args[0],NULL); break;
			case 2: $this->Options[] = new MultiCheckbox_Option ($this->name,$args[0],$args[1]); break;
			default: $this->Creator->debuglog->Write(DEBUG_WARNING,'. . . could not create new Select Option - invalid number of arguments'); break;
		}
		$this->Creator->debuglog->Write(DEBUG_INFO,'. . new Multiple Checkbox Option "'.(isset($args[0])?$args[0]:'').'" created');
		return $this;
	}

	public function addOptionsFromTable () {
		// addOption ( table , value_column , title_column [, where_statement ] )
		$args = func_get_args();
		$options_querystring = "
			SELECT {$args[1]} AS value, {$args[2]} AS title
			FROM {$args[0]}
			ORDER BY {$args[2]}
		";
		$options_query = $this->Creator->Connection->link->query($options_querystring);
		while ($option = $options_query->fetch_object()) {
			$tagged = in_array($option->value, $args[3]);
			$this->Options[] = new MultiCheckbox_Option ($this->name,$option->value,$option->title,$tagged);
			$this->Creator->debuglog->Write(DEBUG_INFO,'. . new Multiple Checkbox Option "'.$option->value.'" created');
		}
		return $this;
	}

	// HTML OUTPUT ---------------------------------------------------------------

	protected function HTMLStyle () {
		return $this->css_style ? " style=\"$this->css_style\"" : NULL;
	}

	public function HTMLOutput () {
		$output = NULL;
		if ($this->is_appended()) {
			$output .= "<label for=\"{$this->getID()}\" class=\"label\">$this->label</label>";
		}
		$output .= "\t\t\t<div";
		$output .= ' id="'.$this->getId().'"';
		if ($this->is_not_hidden()) {
			$output .= $this->HTMLTitle();
			$output .= $this->HTMLClass();
			$output .= $this->HTMLStyle();
		}
		$output .= '>'.PHP_EOL;
		foreach ($this->Options as $option) {
			//TODO delete if not needed
			//$value = $this->getValue();
			// if($option->getValue() == 1) {$is_checked = true;}
			// else {$is_checked = false;}
		  //$is_checked = (isset($this->user_value) && in_array($option->getValue(),$this->user_value));
			$is_checked = $option->getTagged();
		//	$is_checked = ($option->getTagged() || (isset($this->user_value) && in_array($option->getValue(),$this->user_value)));
			$output .= $option->HTMLOutput($is_checked);
		}
		$output .= "\t\t\t</div>".PHP_EOL;
		return $output;
	}

} // end class Field_MultiCheckbox

// SUBORDINATE CLASSES ---------------------------------------------------------

class MultiCheckbox_Option {

	protected $name;
	protected $value;
	protected $title;

	// CONSTRUCTORS --------------------------------------------------------------

	public function __construct ($name,$value,$title,$tagged) {
		$this->name = $name;
		$this->value = $value;
		$this->title = $title;
		$this->tagged = $tagged;
	}

	// PROPERTIES ----------------------------------------------------------------

	public function getValue () {
		return $this->value;
	}

	public function getId () {
		return $this->name.'-'.$this->value;
	}

	public function getTagged () {
		return $this->tagged;
	}

	// HTML OUTPUT ---------------------------------------------------------------

	public function HTMLOutput ($is_checked) {
		$output = "\t\t\t\t<input";
		$output .= ' name="'.$this->name.'[]"';
		$output .= ' id="'.$this->getId().'"';
		$output .= ' type="checkbox"';
		if (isset($this->title)) $output .= ' value="'.$this->value.'"';
		if ($is_checked) $output .= ' checked="checked"';
		$output .= '/> <label for="'.$this->getId().'">';
		if (isset($this->title)) $output .= $this->title;
		else $output .= $this->value;
		$output .= '</label><br/>'.PHP_EOL;
		return $output;
	}

} // end class MultiCheckbox_Option

?>
