<?php
// ZEFIRO DATABASE INTERFACE
// last known update: 2014-02-03

class DBI {

    /**
     *
     * @var mysqli
     */
	public $connection;
	public $maintenance = false;

	protected $View;

	protected $options = array();
	protected $breadcrumbs = array();

	public $user = array();

	// CONSTRUCTOR ---------------------------------------------------------------

	public function __construct ($db_host,$db_user,$db_pass,$db_name) {
	    $this->connection = mysqli_connect($db_host,$db_user,$db_pass,$db_name);
	    if ($this->connection->connect_error) {
			echo L_ERROR_CONNECTION;
			die;
		} else {
		    $this->connection->set_charset('utf8');
			$this->importUserData();
		}
	}

	// USER AUTHENTICATION -------------------------------------------------------

	public function importUserData () {
		// the user must have logged in to retrieve permissions
		if ( isset($_SESSION[Z_SESSION_NAME]['user']) &&
				 isset($_SESSION[Z_SESSION_NAME]['user']['name']) &&
				 isset($_SESSION[Z_SESSION_NAME]['user']['password']) ) {
			$user_querystring = "SELECT * FROM z_users WHERE name = '".$_SESSION[Z_SESSION_NAME]['user']['name']."' AND password = '".$_SESSION[Z_SESSION_NAME]['user']['password']."'";
			$user_query = $this->connection->query($user_querystring);
			if ($user_query->num_rows>0) {
			    return $this->user = $user_query->fetch_array(MYSQLI_ASSOC);
			}
		}
		// the user can also retrieve permissions by a remote address
		elseif ( ($user_querystring = "SELECT * FROM z_users WHERE '".$_SERVER['REMOTE_ADDR']."' LIKE remote") &&
		    ($user_query = $this->connection->query($user_querystring)) &&
		    ($user_query->num_rows>0) ) {
		        return $this->user = $user_query->fetch_array(MYSQLI_ASSOC);
		}
		// if nothing helps, the user is anonymous
		else {
			$user_querystring = "SELECT * FROM z_users WHERE name = 'anonymous'";
			$user_query = $this->connection->query($user_querystring);
			return $this->user = $user_query->fetch_array(MYSQLI_ASSOC);
		}
		return NULL;
	}

	public function checkUserAuthentication () {
		return (isset($_SESSION[Z_SESSION_NAME]['user']['password']));
	}

	public function checkUserPermission ($permission_name) {
		return (
			(isset($_SESSION[Z_SESSION_NAME]['user']['permissions']))
			&&
			(strpos ($_SESSION[Z_SESSION_NAME]['user']['permissions'],$permission_name) !== false)
		);
	}

	public function requireUserAuthentication () {
		if (!$this->checkUserAuthentication()) {
			header('Location: index');
		}
	}

	public function requireUserPermission ($permission_name) {
		if (!$this->checkUserPermission ($permission_name)) {
			header('Location: z_permission');
		}
	}

	// USER DATA -----------------------------------------------------------------

	public function setUserVar() {
		// setUserVar ( var_name , value , default )
		$args = func_get_args();
		if (isset($args[0]) && isset($args[1])) return $this->user[$args[0]] = $args[1];
		elseif (isset($args[2])) return $this->user[$args[0]] = $args[2];
	}

	public function getUserVar( $var_name ) {
		// getUserVar ( var_name )
		if (isset($this->user[$var_name])) return $this->user[$var_name];
		else return NULL;
	}

	// BREADCRUMBS ---------------------------------------------------------------

	public function addBreadcrumb () {
		$this->breadcrumbs[] = func_get_args();
	}

	public function getBreadcrumbs_HTML () {
		if (!isServerScriptName('index.php')) {
			$this->addBreadcrumb($GLOBALS['layout']->get('title'));
		}
		$html = NULL;
		$html .= '<a href="./">'.L_HOME.'</a>';
		foreach($this->breadcrumbs as $index => $crumb) {
			switch (count($crumb)) {
				case 1:
					$html .= Z_BREADCRUMB_SYMBOL.$crumb[0];
					break;
				case 2:
					$html .= Z_BREADCRUMB_SYMBOL.'<a class="breadcrumb" href="'.$crumb[1].'">'.$crumb[0].'</a>';
					break;
				case 3:
					$html .= Z_BREADCRUMB_SYMBOL.'<div class="breadcrumb"><span class="tooltip-text">'.$crumb[2].'</span><a href="'.$crumb[1].'">'.$crumb[0].'</a></div>';
					break;
			}
		}
		return $html;
	}

	// TOOLBAR -------------------------------------------------------------------

	public function addOption () {
		// 0: text
		// 1: link
		// 2: image
		$args = func_get_args();
		$this->options[] = array ($args[0],$args[1],$args[2]);
	}

	public function showOptions () {
		reset ($this->options);
		$options = array();
		foreach($this->options as $index => $option) {
			$options[] =
				'<li'.(isset($option[2])?' class="icon '.$option[2].'"':'').'>'
				.'<a href="'.$option[1].'">'.$option[0].'</a></li>'.PHP_EOL;
		}
		echo '<ul>'.PHP_EOL.implode (Z_SEPARATOR_SYMBOL,$options).'</ul>'.PHP_EOL;
	}

	public function addLoginOption () {
		if (!isServerScriptName('z_login.php') && !isServerScriptName('z_logout.php')) {
			if (isset($_SESSION[Z_SESSION_NAME]['user']) && isset($_SESSION[Z_SESSION_NAME]['user']['name']) && isset($_SESSION[Z_SESSION_NAME]['user']['password'])) {
				$this->addOption (L_LOGOUT,'z_logout','logout');
			}
			else {
				$this->addOption (L_LOGIN,'z_login','login');
			}
		}
	}

	// VIEWS ---------------------------------------------------------------------

	public function getListView ( ) {
		// getListView ( name [, parameter] )
		$args = func_get_args();
		require_once 'dbi_view.php';
		require_once 'dbi_view_list.php';
		require_once 'custom/views/dbi_view_list_'.$args[0].'.php';
		$this->View = call_user_func('view_list_'.$args[0].'::create',$this);
		switch (count($args)) {
			case 1: return $this->View->get_HTML (); break;
			case 2: return $this->View->get_HTML ($args[1]); break;
			default: return false;
		}
	}

	public function getRecordView ( ) {
		// getListView ( name [, parameter] )
		$args = func_get_args();
		require_once 'dbi_view.php';
		require_once 'dbi_view_record.php';
		require_once 'custom/views/dbi_view_record_'.$args[0].'.php';
		$this->View = call_user_func('view_record_'.$args[0].'::create',$this);
		switch (count($args)) {
			case 1: return $this->View->get_HTML (); break;
			case 2: return $this->View->get_HTML ($args[1]); break;
			default: return false;
		}
	}

	// TEXTBLOCKS --------------------------------------------------------------

	public function getTextblock_HTML ($name) {
		$textblock_querystring = "
			SELECT t.textblock_id, t.name, t.permission,
				t.title_".USER_LANGUAGE." AS title,
				t.content_".USER_LANGUAGE." AS content
			FROM z_textblocks t
			WHERE t.name='{$name}'
		";
		$textblock_query = $this->connection->query($textblock_querystring);
		$html = NULL;
		if ($textblock = $textblock_query->fetch_object()) {
			if ($textblock->title) $html .= '<h4>'.$textblock->title.'</h4>'.PHP_EOL;
			$html .= SimpleMarkup_HTML($textblock->content);
			if ($this->checkUserPermission($textblock->permission)) {
				$html .= '<p>'.createSmallButton(L_EDIT,'z_edit_textblock?textblock_id='.$textblock->textblock_id,'icon edit').'</p>'.PHP_EOL;
			}
		}
		else {
			if ($this->checkUserPermission('system')) {
				$html .= '<p>'.createSmallButton(L_NEW_TEXTBLOCK,'z_edit_textblock?name='.$name,'icon addTextblock').'</p>'.PHP_EOL;
			}
		}
		return $html;
	}

}
