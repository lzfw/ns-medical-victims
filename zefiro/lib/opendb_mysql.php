<?php

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
