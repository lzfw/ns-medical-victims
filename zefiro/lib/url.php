<?php

function getUrlParameter ($parameterName, $default = NULL) {
	return ((isset($_GET[$parameterName]) && $_GET[$parameterName]!='') ? $_GET[$parameterName] : $default);
}

function isUrlParameter ($parameterName) {
	return isset($_GET[$parameterName]);
}

// CSRF token of the session (see zefiro/ini.php)
function csrfToken () {
	return $_SESSION[Z_SESSION_NAME]['csrf_token'];
}

// escape a value for use inside an SQL string literal ('...')
function escapeSQL ($value) {
	global $dbi;
	return $dbi->connection->real_escape_string((string) $value);
}

// export filters stay on the server; links pass only the id
function registerExportWhereClause ($where_clause) {
	$id = substr(hash('sha256', $where_clause), 0, 16);
	if (!isset($_SESSION[Z_SESSION_NAME]['export_where']))
		$_SESSION[Z_SESSION_NAME]['export_where'] = array();
	$store =& $_SESSION[Z_SESSION_NAME]['export_where'];
	unset($store[$id]);
	$store[$id] = $where_clause;
	$store = array_slice($store, -50, NULL, true); // keep the latest searches
	return $id;
}

function getExportWhereClause ($id) {
	if (!is_string($id)) return NULL;
	return $_SESSION[Z_SESSION_NAME]['export_where'][$id] ?? NULL;
}

?>