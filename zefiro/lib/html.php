<?php

// generic functions

function buildElement () {
	$args = func_get_args();
	switch (func_num_args()) {
		case 2: // 0:name, 1:content
			return
				'<'.$args[0].'>'.PHP_EOL.
				$args[1].
				'</'.$args[0].'>'.PHP_EOL;

		case 3: // 0:name, 1:class/id, 2:content
			return
				'<'.$args[0].
				($args[1][0] == '#'
					? ' id="'.substr($args[1],1).'"'
					: ' class="'.$args[1].'"').
				'>'.PHP_EOL.
				$args[2].
				'</'.$args[0].'>'.PHP_EOL;
		default:
			return false;
	}
}

function buildRow($rowElementName, $cellElementName, $cellContentArray) {
	// hier die Parameter noch flexibilisieren
	$cellHtml = '';
	foreach ($cellContentArray as $cellContent) {
		$cellHtml .= '<'.$cellElementName.'>'.$cellContent.'</'.$cellElementName.'>'.PHP_EOL;
	}
	return buildElement($rowElementName,$cellHtml);
}

/**
 * Create a row for a data sheet containing a key in a heading cell and a value
 * in a regular HTML table cell.
 *
 * @param string      $thContent  key/heading (must not contain insecure content)
 * @param string|null $tdContent  value/content
 * @param mixed  $entityConversionFlags  FALSE if no HTML special characters
 *                                       should be converted to HTML entities,
 *                                       otherwise flags for htmlspecialchars
 * @return string
 */
function buildDataSheetRow (string $thContent, ?string $tdContent, $entityConversionFlags = ENT_HTML5) {
    $tdContent = $entityConversionFlags === FALSE
        ? $tdContent
        : htmlspecialchars($tdContent, $entityConversionFlags);

    return
        buildElement('tr',
            buildElement('th',$thContent).
            buildElement('td',$tdContent)
        );
}

// query functions

// single values

function getValueFromQuery($querystring) {
    global $dbi;

    $result = $dbi->connection->query($querystring);
	if ($result) {
		// nur erste Zeile, erste Zelle
		$row = $result->fetch_array(MYSQLI_NUM);
		return $row[0];
	}
	else return '[invalid query]';
}

// tables and rows

function buildRowsFromQuery($rowElementName, $cellElementName, $querystring, $maskArray) {
    global $dbi;

    $result = $dbi->connection->query($querystring);
	if ($result) {
		$html = NULL;
		while ($row = $result->fetch_object()) {
			$html .= '<'.$rowElementName.'>'.PHP_EOL;
			foreach ($maskArray as $mask) {
				// ersetzt jedes {x} durch $row->x
				$cellElementContent = preg_replace_callback('/(\{(\w*)\})/',
				    function ($matches) use ($row) {
				        return $row->{$matches[2]};
				    }, $mask);
				if ($cellElementContent != NULL) :
					$html .= '<'.$cellElementName.'>'.$cellElementContent.'</'.$cellElementName.'>'.PHP_EOL;
				else :
					$html .= '<'.$cellElementName.'> - </'.$cellElementName.'>'.PHP_EOL;
				endif;
			}
			$html .= '</'.$rowElementName.'>'.PHP_EOL;
		}
		return $html;
	}
	else return 'invalid query';
}

function buildTableFromQuery () {
	$args = func_get_args();
	switch (func_num_args()) {

		case 2: // 0:querystring, 1:mask
			return buildElement('table',
				buildRowsFromQuery('tr','td',$args[0],$args[1])
			);

		case 3: // 0:querystring, 1:mask, 2:headerArray
			return buildElement('table',
				buildRow('tr','th',$args[2]).
				buildRowsFromQuery('tr','td',$args[0],$args[1])
			);

		case 4: // 0:querystring, 1:mask, 2:headerArray, 3:class/Id
			return buildElement('table',$args[3],
				buildRow('tr','th',$args[2]).
				buildRowsFromQuery('tr','td',$args[0],$args[1])
			);

		default: return false;
	}

}

function buildSheetFromQuery () {
    // TODO
    // das sollte eine vertikal angeordnete Tabelle sein
    // also Feldnamen in der erstn Spalte
	$args = func_get_args();
	switch (func_num_args()) {
		default: return false;
	}
}


// lists

function buildListFromQuery($listElementName, $querystring, $mask) {
    global $dbi;

    $result = $dbi->connection->query($querystring);
	if ($result) {
		$html = NULL;
		while ($row = $result->fetch_object()) {
			// ersetzt jedes {x} durch $row->x
		    $listElementContent = preg_replace_callback('/(\{(\w*)\})/',
		        function ($matches) use ($row) {
		            return $row->{$matches[2]};
		        }, $mask);
			$html .= '<'.$listElementName.'>'.$listElementContent.'</'.$listElementName.'>'.PHP_EOL;
		}
		return $html;
	}
	else return 'invalid query';
}

function buildUlFromQuery () {
	$args = func_get_args();
	switch (func_num_args()) {

		case 2: // 0:querystring, 1:mask
			return buildElement('ul',
				buildListFromQuery('li',$args[0],$args[1])
			);

		case 3: // 0:class/Id, 1:querystring, 2:mask
			return buildElement('ul',$args[0],
				buildListFromQuery('li',$args[1],$args[2])
			);

		default: return false;
	}

}

function buildOlFromQuery () {
	$args = func_get_args();
	switch (func_num_args()) {

		case 2: // 0:querystring, 1:mask
			return buildElement('ol',
				buildListFromQuery('li',$args[0],$args[1])
			);

		case 3: // 0:class/Id, 1:querystring, 2:mask
			return buildElement('ol',$args[0],
				buildListFromQuery('li',$args[1],$args[2])
			);

		default: return false;
	}

}
