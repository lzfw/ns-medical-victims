<?php

/**
 * Database Connector.
 *
 * @var string $db_name Name of database to connect with.
 * @var DatabaseConnectionCreator $Creator
 *
 */
abstract class Connection {

	protected $Creator;
	protected $db_name;

	public $link;
	protected $primary_key_name;

	// PROPERTIES ----------------------------------------------------------------
	function setPrimaryKeyName ($pk_name) {
		$this->primary_key_name = $pk_name;
		$this->Creator->addField ($pk_name,HIDDEN);
	}

	function setPrimaryKeyValue ($pk_value) {
		$this->Creator->Fields[$this->primary_key_name]->user_value = $pk_value;
	}

	function getPrimaryKeyName () {
		return ($this->primary_key_name);
	}

	function getPrimaryKeyValue () {
		return ($this->Creator->Fields[$this->primary_key_name]->user_value);
	}
}

