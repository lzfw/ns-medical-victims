<?php
// Zefiro Template Initialization Script
// last known update: 2013-02-04

require_once 'custom/templates/header.tpl.php';
if ($dbi->maintenance) {
	require_once 'custom/templates/body.maintenance.tpl.php';
}
else {
	$getView = getUrlParameter('view');
	switch ($getView) {
		case 'print':
			require_once 'custom/templates/body.print.tpl.php';
			break;
		default:
			require_once 'custom/templates/body.standard.tpl.php';
			break;
	}
}
require_once 'custom/templates/footer.tpl.php';
?>