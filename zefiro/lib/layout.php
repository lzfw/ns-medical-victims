<?php
// LAYOUT INITIALIZATION
// last known update: 2014-01-27

// PHP DEBUGGING INFO

// uncomment this line in productive environment
//ini_set('display_errors', 0);
//error_reporting(0);

// uncomment this line in development environment
ini_set('display_errors', 1);
error_reporting(E_ALL);

abstract class Variable {
	
	public $value;
	
	public function getType () {
		return get_class($this);
	}
	
	public function set ( $value ) { 
		$this->value = $value;
	}
	
	public function get () {
		return $this->value;
	}
	
}

class StringType extends Variable {

	public function cast () {
		echo $this->value;
	}
}

class BlockType extends Variable {
	
	public $element;
	public $id;
	
	public function __construct ($element = NULL, $id = NULL) {
		$this->element = $element;
		$this->id = $id;
	}
	
	public function cast () {
		echo
			($this->element ? '<'.$this->element.
				( $this->id ? ' id="'.$this->id.'"' : '').
				'>' : '').
			$this->value.
			($this->element ? '</'.$this->element.'>' : '');
	}
}

class ListType extends Variable {
}

class Layout {
	
	public $Variables = array();
	public $template;
	
	// CONSTRUCTORS -----------------------------------------------------------
	
	public function __construct () {
	}
	
	public function setTemplate ( $url ) {
		$this->template = $url;
	}
	
	public function declareString ($name) {
		$this->Variables[$name] = new StringType ($value);
		return $this;
	}
	
	public function declareBlock ($name, $element = NULL, $id = NULL) {
		$this->Variables[$name] = new BlockType ($element, $id);
		return $this;
	}
	
	public function declareList ($name, $value = NULL) {
		$this->Variables[$name] = new ListType ($value);
		return $this;
	}
	
	public function set ( $varName, $varValue ) {
		$this->Variables[$varName]->set($varValue);
		return $this;
	}
	
	public function get ( $varName ) {
		return $this->Variables[$varName]->get();
	}
	
	public function cast ( $varName = NULL ) {
		if ($varName) {
			$this->Variables[$varName]->cast();
		}
		else {
			if (!@require_once($this->template)) die ('failed to include template');
			//require_once $this->template;
			//echo $this->template;
		}
	}
	
} // end class LAYOUT

?>