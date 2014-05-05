<?php

abstract class View {
	protected $Creator;

	protected function __construct ($Creator) {
		$this->Creator = $Creator;
	}
	
}

?>
