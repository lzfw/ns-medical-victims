<?php

/**
 * Open a new database connection
 *
 * @param string $db_host
 * @param string $db_user
 * @param string $db_pass
 * @param string $db_name
 * @return boolean|mysqli
 */
function openDB($db_host,$db_user,$db_pass,$db_name) {
    $link = @new mysqli($db_host,$db_user,$db_pass,$db_name);
    if ($link->connect_error) {
		die ('MYSQL : connection failed');
		return false;
	} else {
		// connection was established;
		// charset
	    $link->set_charset('utf8');
		return $link;
	}
}
