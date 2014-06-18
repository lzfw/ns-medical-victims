<?php
// Zefiro Template Initialization Script
// last known update: 2013-02-04

$dbi =& $GLOBALS['dbi'];
require_once 'custom/layout/template.header.php';
if ($dbi->maintenance) {
	require_once 'custom/layout/template.body.maintenance.php';
}
else {
	$getView = getUrlParameter('view');
	switch ($getView) {
		case 'print':
			require_once 'custom/layout/template.body.print.php';
			break;
		default:
			require_once 'custom/layout/template.body.standard.php';
			break;
	}
}
require_once 'custom/layout/template.footer.php';
?>