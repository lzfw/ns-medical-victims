<?php

/**
 * manages the communication between this application and the database
 *
 *
 */

class DBI {

    /**
     *
     * @var mysqli
     */
    public $connection;
    public $maintenance = false;
    protected $view;
    protected $options = [];
    protected $breadcrumbs = [];

    public $user = [];

    /**
     * Constructor for DBI class.
     *
     * @param $db_host
     * @param $db_user
     * @param $db_pass
     * @param $db_name
     */
    public function __construct ($db_host, $db_user, $db_pass, $db_name) {
        $this->connection = mysqli_connect($db_host,$db_user,$db_pass,$db_name);
        if ($this->connection->connect_error) {
            echo L_ERROR_CONNECTION;
            die;
        } else {
            $this->connection->set_charset('utf8');
            $this->importUserData();
        }
    }


    /**
     * Imports user data for authentication from database.
     *
     * @return array|false|null
     */
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

    //OBACHT

    /**
     * Checks if user is authenticated.
     *
     * @return bool Indicates if user is authenticated, returns true if so and false if not.
     */
    public function checkUserAuthentication (): bool
    {
        return true;
        //return (isset($_SESSION[Z_SESSION_NAME]['user']['password']));
    }

    // OBACHT

    /**
     * Checks if user has a certain permission which is passed via parameter.
     *
     * @param string $permission_name Requested kind of permission, for example permission to edit or to view.
     * @return bool Indicates if user has requested permission, returns true if so and false if not.
     */
    public function checkUserPermission (string $permission_name): bool
    {
        if($permission_name == 'mpg') :
            return false;
        else :
            return true;
        endif;
        // return (
        // 	(isset($_SESSION[Z_SESSION_NAME]['user']['permissions']))
        // 	&&
        // 	(strpos ($_SESSION[Z_SESSION_NAME]['user']['permissions'],$permission_name) !== false)
        // );
    }

    /**
     * Requests check if user is authenticated.
     *
     * Returns header if user isn't authenticated.
     *
     * @see method checkUserAuthentication
     * @return void
     */
    public function requireUserAuthentication () {
        if (!$this->checkUserAuthentication()) {
            header('Location: index');
        }
    }

    /**
     *
     * Requests check of user permission and grants access if it matches $permission_name.
     *
     * @see method checkUserPermission
     * @param string $permission_name Name of the required permission.
     * @return void
     */
    public function requireUserPermission (string $permission_name) {
        if (!$this->checkUserPermission ($permission_name)) {
            header('Location: z_permission');
        }
    }

    /**
     * Requests check of user permission and denies access in case it matches $permission_name.
     *
     *
     * @param string $permission_name Name of denied permission.
     * @return void
     *@see method checkUserPermission
     */
    public function denyUserPermission (string $permission_name) {
        if ($this->checkUserPermission ($permission_name)) {
            header('Location: z_permission');
        }
    }



    /**
     * Sets variable (name and value) via URL.
     *
     * @return mixed|void
     */
    public function setUserVar() {
        // setUserVar ( var_name , value , default )
        $args = func_get_args();
        if (isset($args[0]) && isset($args[1])) return $this->user[$args[0]] = $args[1];
        elseif (isset($args[2])) return $this->user[$args[0]] = $args[2];
    }

    /**
     * Gets variable from URl.
     *
     * @param string $var_name Name of variable to get from URL
     * @return mixed|null Returns null if variable is not set, otherwise returns variable.
     */
    public function getUserVar($var_name ) {
        // getUserVar ( var_name )
        if (isset($this->user[$var_name])) return $this->user[$var_name];
        else return NULL;
    }


    /**
     * Adds breadcrumb to breadcrumb array.
     *
     * @return void
     */
    public function addBreadcrumb () {
        $this->breadcrumbs[] = func_get_args();
    }

    /**
     * Creates html code with breadcrumb links.
     *
     * @return string $html
     */
    public function getBreadcrumbs_HTML (): string
    {
        if (!isServerScriptName('index.php')) {
            $this->addBreadcrumb($GLOBALS['layout']->get('title'));
        }
        $html = NULL;
        $html .= '<a href="./">'.L_HOME.'</a>';
        foreach($this->breadcrumbs as $index => $crumb) {
            switch (count($crumb)) {
                case 1:
                    $html .= ' '.Z_BREADCRUMB_SYMBOL.$crumb[0];
                    break;
                case 2:
                    $html .= ' '.Z_BREADCRUMB_SYMBOL.'<a class="breadcrumb" href="'.$crumb[1].'">'.$crumb[0].'</a>';
                    break;
                case 3:
                    $html .= ' '.Z_BREADCRUMB_SYMBOL.'<div class="breadcrumb"><span class="tooltip-text">'.$crumb[2].'</span><a href="'.$crumb[1].'">'.$crumb[0].'</a></div>';
                    break;
            }
        }
        return $html;
    }

    // NAVIGATION / TOOLBAR -------------------------------------------------------------------

    /**
     * Adds element array to options array which is used for the creation of the navigation.
     *
     * Element array can consist of 3 strings. The first string is the text of the link,
     * the second string is the url slug and the third specifies an icon.
     *
     * @return void
     */
    public function addOption () {
        // 0: text
        // 1: link
        // 2: image
        $args = func_get_args();
        $this->options[] = [$args[0],$args[1],$args[2]];
    }

    // OBACHT

    /**
     * Creates html of navigation for options array.
     *
     * @return void
     */
    public function showOptions () {
        reset ($this->options);
        $options = array();
        foreach($this->options as $index => $option) {
            $options[] =
                '<li'.(isset($option[2])?' class="icon '.$option[2].'"':'').'>'
                .'<a href="'.$option[1].'.php'.'">'.$option[0].'</a></li>'.PHP_EOL;
        }
        echo '<ul>'.PHP_EOL.implode (Z_SEPARATOR_SYMBOL,$options).'</ul>'.PHP_EOL;
    }


    // public function showOptions () {
    // 	reset ($this->options);
    // 	$options = array();
    // 	foreach($this->options as $index => $option) {
    // 		$options[] =
    // 			'<li'.(isset($option[2])?' class="icon '.$option[2].'"':'').'>'
    // 			.'<a href="'.$option[1].'">'.$option[0].'</a></li>'.PHP_EOL;
    // 	}
    // 	echo '<ul>'.PHP_EOL.implode (Z_SEPARATOR_SYMBOL,$options).'</ul>'.PHP_EOL;
    // }

    public function addLoginOption () {
        if (!isServerScriptName('z_login.php')) {
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
        $this->view = call_user_func('view_list_'.$args[0].'::create',$this);
        switch (count($args)) {
            case 1: return $this->view->get_HTML (); break;
            case 2: return $this->view->get_HTML ($args[1]); break;
            case 3: return $this->view->get_HTML ($args[1], $args[2]); break;
            default: return false;
        }
    }

    public function getRecordView ( ) {
        // getRecordView ( name [, parameter] )
        $args = func_get_args();
        require_once 'dbi_view.php';
        require_once 'dbi_view_record.php';
        require_once 'custom/views/dbi_view_record_'.$args[0].'.php';
        $this->view = call_user_func('view_record_'.$args[0].'::create',$this);
        switch (count($args)) {
            case 1: return $this->view->get_HTML (); break;
            case 2: return $this->view->get_HTML ($args[1]); break;
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

    // LOG ---------------------------------------------------------------------

    public function log (DBI_Log_Entry $log_entry) {
        $mysqli = $this->connection;

        /* Prepared statement, stage 1: prepare */
        if (!($stmt = $mysqli->prepare(
            "INSERT INTO
                z_log(operation, entity, result, row_id, details)
                VALUES (?, ?, ?, ?, ?)"))) {
            throw new Error(
                "Prepare failed: (" . $mysqli->errno . ") " .
                $mysqli->error);
        }

        /* Prepared statement, stage 2: bind and execute */
        if (!$stmt->bind_param("ssiis",
            $log_entry->operation,
            $log_entry->entity,
            $log_entry->result,
            $log_entry->row_id,
            $log_entry->details)) {
            throw new Error("Binding params failed: (" . $stmt->errno . ") " .
                $stmt->error);
        }

        if (!$stmt->execute()) {
            throw new Error("Execute failed: (" . $stmt->errno . ") " .
                $stmt->error);
        }
    }
}
