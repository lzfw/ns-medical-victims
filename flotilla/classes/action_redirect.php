<?php
// last known update: 2013-01-25

class Action_Redirect extends Action {

	protected $url;

	protected function __construct ($Creator,$url) {
		$args = func_get_args();
		$this->Creator = $Creator;
		$this->url = $url;
		$this->Creator->debuglog->Write(DEBUG_INFO,'. REDIRECT ACTION created');
	}

	static public function create () {
		// create ( Creator )
		$args = func_get_args();
		switch (func_num_args()) {
			case 1: return new Action_Redirect ($args[0],$_SESSION['flotilla']['last_page']);
			case 2: return new Action_Redirect ($args[0],$args[1]);
			default: $this->Creator->debuglog->Write(DEBUG_WARNING,'. REDIRECT ACTION - invalid number of arguments');
		}
	}

	public function onSubmit () {
	    $newLocation = preg_replace_callback('/(\{(\w*)\})/', function ($matches) {
	        return $this->Creator->Fields[$matches[2]]->user_value;
	    }, $this->url);
	    if (isset($_POST['submit-button'])) header('Location: '. $newLocation);
		//if (isset($_POST['submit-button'])) header('Location: '.$this->url);
	}

}

