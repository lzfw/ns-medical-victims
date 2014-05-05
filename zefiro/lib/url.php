<?php

function getUrlParameter ($parameterName, $default = NULL) {
	return ((isset($_GET[$parameterName]) && $_GET[$parameterName]!='') ? $_GET[$parameterName] : $default);
}

function isUrlParameter ($parameterName) {
	return isset($_GET[$parameterName]);
}

?>