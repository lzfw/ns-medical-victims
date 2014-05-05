<?php

function gr_translit_ws_txt ($gr_string) {
	$gr_translit_url = 'http://pdr.bbaw.de/pdrws/g2l?ruleset=en19&method=greekToLatin&token='.$gr_string.'&output=txt';
	$ws_result = file($gr_translit_url);
	$gr_translit = preg_replace('#pdrws\-result\=(.*)#','$1',$ws_result[5]);
	return $gr_translit;
}

function gr_translit_ws_xml ($gr_string) {
	$gr_translit = '';
	$gr_translit_url = 'http://pdr.bbaw.de/pdrws/g2l?ruleset=en19&method=greekToLatin&token='.$gr_string.'&output=xml';
	//$gr_translit_url = 'http://pdr.bbaw.de/pdrws/g2l?ruleset=en19&method=greekToLatin&token='.urlencode($gr_string).'&output=xml';
	$xml = new XMLReader();
	$xml->open($gr_translit_url);
	while ($xml->read()) {
		$gr_translit .= $xml->value;
	}
	$xml->close();
	return $gr_translit;
}

?>