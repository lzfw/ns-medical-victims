<?php
require_once 'zefiro/ini.php';

header('Content-type: text/html; charset=utf-8');

$dbi->requireUserPermission ('view');

// Benutzereingabe
$table = $dbi->connection->escape_string( $_GET['table'] );
$field = $dbi->connection->escape_string( $_GET['field'] );
$input = $dbi->connection->escape_string( $_GET['term'] );
$label = isset($_GET['label'])
    ? $dbi->connection->escape_string($_GET['label'])
    : NULL;

// Suchstring der aktiven Datenansicht
$querystring = "SELECT `$field` AS value";
if ($label) $querystring .= ", `$label` AS label";
$querystring .= " FROM `$table` WHERE `$field` LIKE '%$input%' ORDER BY `$field` LIMIT 0,5";

$jsonList = '[ ';

$query = $dbi->connection->query($querystring);

$jsonStr = '';
while ($result = $query->fetch_object()) {
	$jsonStr .= '{ "value":"'.$result->value.'"';
	if ($label) $jsonStr .= '{ "label":"'.$result->label.'"';
	$jsonStr .= ' },';
}
$jsonStr = rtrim($jsonStr, ',');
$jsonList .= $jsonStr." ]";
echo $jsonList;

