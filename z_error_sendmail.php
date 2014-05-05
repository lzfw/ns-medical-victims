<?php

// sender
$from_address = DBI_REPORTER_MAIL;
$from_name = DBI_REPORTER_NAME;

// address
$to_address = DBI_SITEOWNER_MAIL;
//$to_address = DBI_WEBMASTER_MAIL;

// mail subject
$subject = 'Error Report from '.$_SERVER["SERVER_NAME"];

// mail content
$content = 'Error Report from '.$_SERVER["SERVER_NAME"].PHP_EOL.PHP_EOL;
if (isset($_SERVER["SCRIPT_URI"]))		$content .= 'Script URI:     '.$_SERVER["SCRIPT_URI"].PHP_EOL;
if (isset($_SERVER["QUERY_STRING"]))	$content .= 'Query String:   '.$_SERVER["QUERY_STRING"].PHP_EOL;
if (isset($_SERVER["HTTP_REFERER"]))	$content .= 'Referer:        '.$_SERVER["HTTP_REFERER"].PHP_EOL;
if (isset($_SERVER["STATUS"]))			$content .= 'Server Status:  '.$_SERVER["STATUS"].PHP_EOL;
if (isset($_SERVER["HTTP_USER_AGENT"]))	$content .= 'User Agent:     '.$_SERVER["HTTP_USER_AGENT"].PHP_EOL;
if (isset($_SERVER["REMOTE_ADDR"]))		$content .= 'Remote Address: '.$_SERVER["REMOTE_ADDR"].PHP_EOL;
if (isset($_SERVER["REMOTE_ADDR"]))		$content .= '                http://www.ip-adress.com/whois/'.$_SERVER["REMOTE_ADDR"].PHP_EOL;

// additional mail headers
$additional_headers = 'MIME-Version: 1.0'.PHP_EOL;
$additional_headers .= 'Content-type: text/plain; charset="UTF-8"'.PHP_EOL;
$additional_headers .= 'From: "'.$from_name.'" <'.$from_address.'>'.PHP_EOL;

// additional mail parameters
$additional_parameters = NULL;
// the following parameter depends on host
//$additional_parameters = '-f '.$this->from_email;

// now go, send it
/*
if(empty($additional_parameters))
	mail($to_address, $subject, $content, $additional_headers);
else
	mail($to_address, $subject, $content, $additional_headers, $additional_parameters);
*/
?>
