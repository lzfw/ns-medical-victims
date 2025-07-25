<?php

require_once 'flotilla/lib/opendb_mysql.php';

/**
 * Connects application to MySQL-database.
 *
 */


class Connection_MySQL extends Connection {

    /**
     * Constructor for MySQLConnection.
     *
     * @param Connection_MySQL $Creator DatabaseConnectionCreator - an instance responsible for creating database connections.
     * @param string $db_host Host or URL of Database.
     * @param string $db_user
     * @param string $db_password
     * @param string $db_name
     */
    protected function __construct ($Creator, $db_host, $db_user, $db_password, $db_name) {
		$this->Creator = $Creator;
		$this->db_host = $db_host;
		$this->db_user = $db_user;
		$this->db_password = $db_password;
		$this->db_name = $db_name;
		$this->link = openDB($this->db_host,$this->db_user,$this->db_password,$this->db_name);
		$_SESSION['flotilla']['connection'] = array('flotilla/lib/opendb_mysql.php',$db_host,$db_user,$db_password,$db_name);
		$this->Creator->debuglog->Write(DEBUG_INFO,'. CONNECTION created (mysql)');
	} // end constructor

	static public function create () {
		$args = func_get_args();
		return new Connection_MySQL ($args[0],$args[1],$args[2],$args[3],$args[4]);
	}

	// AUTO FIELDS ---------------------------------------------------------------

	function sendFields ($table_name) {
		$querystring = "SHOW COLUMNS FROM $table_name";
		$query = $this->link->query($querystring);
		while ($column = $query->fetch_object()) {
			//print_r($column);
			$name = $column->Field;
			$type = preg_split ('/[\(|\)| ]/',$column->Type,-1,PREG_SPLIT_NO_EMPTY);
			$length = $type[1];
			if ($column->Key == 'PRI') {
				$default = NULL;
				$this->Creator->addField ($name,HIDDEN);
				$this->setPrimaryKeyName ($name);
			} else {
				$default = $column->Default;
				$this->Creator->addField ($name,TEXT,$length,$default);
			}
		}
	}

	// PRIMARY KEY ---------------------------------------------------------------

	function getPrimaryKey ($table_name) {
		$querystring = "SHOW COLUMNS FROM $table_name";
		$query = $this->link->query($querystring);
		while ($column = $query->fetch_object()) {
			if (strpos($column->Type,'[PRI]')===true) {
				return $column->Field;
			}
		}
		return NULL;
	}

} // end class Connection

