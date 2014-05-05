<?php
// last known update: 2013-01-22

function createHomeLink () {
	return '<p class="icon home"><a href="index">'.MY_HOME.'</a></p>'.PHP_EOL;
}

function createBackLink ($title,$url = 'javascript:history.back()') {
	return '<p class="icon back"><a href="'.$url.'">'.$title.'</a></p>'.PHP_EOL;
}

function isServerScriptName ($string) {
	return basename($_SERVER['SCRIPT_NAME']) == $string; 
}

function getPostValue ($parameter_name) {
	// getPostValue ( parameter_name )
	return ((isset($_POST[$parameter_name]) && $_POST[$parameter_name]!='')?$_POST[$parameter_name]:NULL);
}

function getVirtualKeyboard () {
	return '<script type="text/javascript" src="javascript/keyboard/keyboard_gga.js" charset="utf-8"></script>';
}

function file_exists_grep ($dir, $file) {
	$ret = exec("ls ".$dir." | grep ".$file);
	return (!empty($ret));
}

function roman_numeral ($integer) {
	$roman = array(NULL,'I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII');
	return $roman[$integer];
}

function mysql_get_random_row($table, $column) {
	$rows_querystring = "SELECT max($column) AS max_id, min($column) AS min_id FROM $table";
	$rows = mysql_fetch_object(mysql_query($rows_querystring));
	$random_number = mt_rand($rows->min_id, $rows->max_id);
	$random_sql = "SELECT * FROM $table
		WHERE $column >= $random_number 
		ORDER BY $column ASC LIMIT 1";
	$random_row = mysql_fetch_object(mysql_query($random_sql));
	if (!is_array($random_row)) {
		$random_sql = "SELECT * FROM $table
			WHERE $column < $random_number 
			ORDER BY $column DESC LIMIT 1";
		$random_row = mysql_fetch_object(mysql_query($random_sql));
	}
	return $random_row;
}

function parseSimpleMarkup ($text) {
	// **bold**
	$text = preg_replace ('#\*\*([^\*]+)\*\*#', '<b>$1</b>', $text);
	$text = preg_replace ('#\*\~\*#', '**', $text);
	// __italics__
	$text = preg_replace ('#\_\_([^\_]+)\_\_#', '<i>$1</i>', $text);
	$text = preg_replace ('#\_\~\_#', '__', $text);
	// [link-title [http://link-address]]
	$text = preg_replace ('#\[([^\[\]]+)\ \[(http\:\/\/[^\[\]]+)\]\]#', '<a href="$2" target="_blank">$1</a>', $text);
	// [link-title [mailto:mail@address]]
	$text = preg_replace ('#\[([^\[\]]+)\ \[mailto\:([^\[\]]+)\]\]#', '<a href="mailto:$2" target="_blank">$1</a>', $text);
	// [link-title [link-address]]
	$text = preg_replace ('#\[([^\[\]]+)\ \[([^\[\]]+)\]\]#', '<a href="$2">$1</a>', $text);
	// [[http://link-address]]
	$text = preg_replace ('#\[\[(http\:\/\/[^\[\]]+)\]\]#', '<a href="$1" target="_blank">$1</a>', $text);
	// [[mailto:mail@address]]
	$text = preg_replace ('#\[\[mailto\:([^\[\]]+)\]\]#', '<a href="mailto:$1" target="_blank">$1</a>', $text);
	// [[link-address]]
	$text = preg_replace ('#\[\[([^\[\]]+)\]\]#', '<a href="$1">$1</a>', $text);
	// [~[ ]~] link symbol escape
	$text = preg_replace ('#\[\~\[#', '[[', $text);
	$text = preg_replace ('#\]\~\]#', ']]', $text);
	// ##image##
	$text = preg_replace ('%##([^#]*)##%', '<img src="files/images/$1" alt=""/>', $text);
	$text = preg_replace ('%#\~#%', '##', $text);
	// carriage return win
	$text = preg_replace ('#\r\n#', '<br/>', $text);
	// carriage return mac
	$text = preg_replace ('#\r#', '<br/>', $text);
	// carriage return unix
	$text = preg_replace ('#\n#', '<br/>', $text);
	// <--> <-- --> single line arrows
	$text = preg_replace ('#\<\-\-\>#', '&harr;', $text);
	$text = preg_replace ('#\<\-\-#', '&rarr;', $text);
	$text = preg_replace ('#\-\-\>#', '&larr;', $text);
	// <==> <== ==> double line arrows
	$text = preg_replace ('#\<\=\=\>#', '&hArr;', $text);
	$text = preg_replace ('#\<\=\=#', '&rArr;', $text);
	$text = preg_replace ('#\=\=\>#', '&lArr;', $text);
	// -- en dash
	$text = preg_replace ('#\-\-#', '&ndash;', $text);
	$text = preg_replace ('#\-\~\-#', '--', $text);
	// -? soft hyphen
	$text = preg_replace ('#\-\?#', '&#x00AD;', $text);
	$text = preg_replace ('#\-\~\?#', '-?', $text);
	// -! non-breaking hyphen
	$text = preg_replace ('#\-\!#', '&#x2011;', $text);
	$text = preg_replace ('#\-\~\!#', '-!', $text);
	// :: bullet
	$text = preg_replace ('#\:\:#', '&bull;', $text);
	$text = preg_replace ('#\:\~\:#', '::', $text);
	// ++ diamond
	$text = preg_replace ('#\+\+#', '&diams;', $text);
	$text = preg_replace ('#\+\~\+#', '++', $text);
	// $C copyright
	$text = preg_replace ('#\$C#', '&copy;', $text);
	$text = preg_replace ('#\$\~C#', '$C', $text);
	// $P paragraph
	$text = preg_replace ('#\$P#', '&para;', $text);
	$text = preg_replace ('#\$\~P#', '$P', $text);
	return $text;
}

function SimpleMarkup_HTML($text) {
	return '<p class="simplemarkup">'.parseSimpleMarkup($text).'</p>';
}

function SimpleParagraphs_HTML($text) {
	return '<p>'.parseSimpleMarkup($text).'</p>';
}

function SimpleLists_HTML($text) {
	return '<li>'.parseSimpleMarkup($text).'</li>';
}

function getDateUserStamp_HTML ($stamp,$date,$user) {
	// this works best with dates from 1970-01-01 on
	// in other cases, use historicalDateFormat
	$html = '<p class="small">';
	if (($date != '0000-00-00') || $user) {
		$html .= ''.$stamp.'';
		if ($user) $html .= Z_STAMP_BY_USER.$user;
		if ($date != '0000-00-00') {
			$replacements = array();
			$patterns = array('/ISO 8859/', '/Y/', '/m/', '/d/', '/M/');
			preg_match('/([0-9]{4})-([0-9]{2})-([0-9]{2})/',$date,$replacements);
			// local month name
			$replacements[4] = strftime('%B',mktime(0,0,0,$replacements[2]));
			$html .= Z_STAMP_ON_DATE.preg_replace ($patterns, $replacements, Z_DATE_FORMAT);
		}
		$html .= '</p>'.PHP_EOL;
	}
	return $html;
}

function historicalDateFormat ($date) {
	// attention - this might produce errors
	$replacements = array();
	$patterns = array('/ISO 8859/', '/Y/', '/m/', '/d/', '/M/');
	preg_match('/([0-9]{4})-([0-9]{2})-([0-9]{2})/',$date,$replacements);
	// local month name
	$replacements[4] = strftime('%B',mktime(0,0,0,$replacements[2]));
	if ($replacements[1] != '0000' && $replacements[2] != '00' && $replacements[3] != '00') {
		return preg_replace ($patterns, $replacements, Z_DATE_FORMAT);
	}
	elseif ($replacements[1] != '0000' && $replacements[2] != '00') {
		return preg_replace ($patterns, $replacements, Z_YEAR_MONTH_FORMAT);
	}
	elseif ($replacements[1] != '0000') {
		return preg_replace ($patterns, $replacements, Z_YEAR_FORMAT);
	}
	else {
		return NULL;
	}
}

function mysql_backup ($tableNames,$appendix='') {
	// tableNames: comma-separated list of 1 or more table names
	// appendix: additional command for table selects (WHILE, LIMIT, ...)
	$tableNames = explode (',',$tableNames);
	$backupString = '';
	foreach ($tableNames as $tableName) {
		// get show create table
		$qs = "SHOW CREATE TABLE `$tableName`";
		$results = mysql_query($qs);
		while ($result = mysql_fetch_assoc($results)) {
			$backupString .= $result['Create Table'].';'.PHP_EOL;
		}
		
		// get field names
		$qs = "SHOW COLUMNS FROM `$tableName`";
		$results = mysql_query($qs);
		$fieldNames = array();
		while ($result = mysql_fetch_assoc($results)) {
			$fieldNames[] = $result['Field'];
		}
		
		// get table content
		$qs = "SELECT * FROM `$tableName` ".$appendix;
		$results = mysql_query($qs);
		$tableContents = array();
		
		$values = array();
		while ($row=mysql_fetch_row($results)) {
			$temp=array();
			foreach ($row as $key=>$value) {
				$temp[$key]="'".$value."'";
			}
			$values[]="(".implode(",",$temp).")";
		}
		
		$backupString .=
			"INSERT INTO `{$tableName}` (`".implode("`,`",$fieldNames)."`)".PHP_EOL.
			"VALUES".PHP_EOL.
			implode(",".PHP_EOL,$values).";".PHP_EOL;
	}
	return $backupString;
}

?>
