<?php
require_once 'zefiro/ini.php';

header('Content-type: text/html; charset=utf-8');

// Benutzereingabe
$table = $_GET['table'];
$field = $_GET['field'];
$input = $_GET['term'];
$label = isset($_GET['label'])?$_GET['label']:NULL;

// Suchstring der aktiven Datenansicht
$querystring = "SELECT `$field` AS value";
if ($label) $querystring .= ", `$label` AS label";
$querystring .= " FROM `$table` WHERE `$field` LIKE '%$input%' ORDER BY `$field` LIMIT 0,5";

$jsonList = '[ ';

//Initialisierung des SOLR Interface
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

