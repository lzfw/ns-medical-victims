<?php

require_once 'zefiro/ini.php';
require_once 'zefiro/lib/opendb_mysql.php';
require_once 'zefiro/lib/url.php';

$dbi->requireUserPermission ('admin');

function remove_record($id_param, $id_col, $table, $subject, $successLocation, $abortLocation, $promt, $breadcrumb, $redir = NULL) {
    global $dbi, $layout;

    $record_id = (int) getUrlParameter($id_param, 0);
    $user_confirm = getUrlParameter('confirm', NULL);

    $sql = "SELECT *
        FROM $table
        WHERE $id_col = ?";

    if ($stmt = $dbi->connection->prepare($sql)) {
        if ( $stmt->bind_param('i', $record_id) ) {
            if ( $stmt->execute() ) {
                $result = $stmt->get_result();
            } else {
                throw new RuntimeException("Can not execute query: " .
                    implode(': ', $stmt->error_list) .
                    ' / #' . $stmt->errno . ' / ' . $stmt->error);
            }
        } else {
            throw new RuntimeException("Can not bind ID parameter: " .
                implode(': ', $stmt->error_list) .
                ' / #' . $stmt->errno . ' / ' . $stmt->error);
        }
    } else {
        var_dump($dbi->connection->error);
        throw new RuntimeException("Can not prepare query: " .
            implode(': ', $dbi->connection->error_list) .
            ' / #' . $dbi->connection->errno . ' / ' . $dbi->connection->error);
    }

    $item = $result->fetch_object();

	switch ($user_confirm) {

		case 'yes':
			// deletion confirmed
			$sql =  "DELETE
		        FROM $table
		        WHERE $id_col = ?
		        LIMIT 1";

            
            if ($stmt = $dbi->connection->prepare($sql)) {
                if ( $stmt->bind_param('i', $record_id) ) {
                    if ( $stmt->execute() ) {
                        $result = $stmt->get_result();
                    } else {
                        throw new RuntimeException("Can not execute query: " .
                            implode(': ', $stmt->error_list) .
                            ' / #' . $stmt->errno . ' / ' . $stmt->error);
                    }
                } else {
                    throw new RuntimeException("Can not bind ID parameter: " .
                        implode(': ', $stmt->error_list) .
                        ' / #' . $stmt->errno . ' / ' . $stmt->error);
                }
            } else {
                var_dump($dbi->connection->error);
                throw new RuntimeException("Can not prepare query: " .
                    implode(': ', $dbi->connection->error_list) .
                    ' / #' . $dbi->connection->errno . ' / ' . $dbi->connection->error);
            }
            $successLocation .= (substr($successLocation, -1) === '=') ? $record_id : '';
            if ($redir) {
                $successLocation = $redir($dbi, 'yes', $successLocation, $item);
            }
			header("Location: $successLocation");
			exit;

		case 'no':
			// deletion cancelled
			$abortLocation .= (substr($abortLocation, -1) === '=') ? $record_id : '';
            if ($redir) {
                $abortLocation = $redir($dbi, 'no', $abortLocation, $item);
            }
			header("Location: $abortLocation");
			exit;

		default:
			break;
	}

    if ($item) {
        $promptText = $promt($dbi, $item);
        $breadcrumb($dbi, $item, $record_id);
    } else {
        $promptText = "The $subject you selected was not found.
        Maybe it is already deleted? Please refresh the invoking page.";
    }

    $layout
    	->set('title', "Remove $subject")
    	->set('content',
    		buildElement('p', $promptText).
    		($item ?
    		    (buildElement('p', L_ARE_YOU_SURE).
        		buildElement('div', 'buttons',
        			createButton (L_NO_CANCEL,
        			    $_SERVER['SCRIPT_NAME'] .
        			        "?$id_param=$record_id&confirm=no",
        			    'icon no').
        			createButton (L_YES_CONTINUE,
        		    	$_SERVER['SCRIPT_NAME'] .
        		    	    "?$id_param=$record_id&confirm=yes",
        		    	'icon yes')
    		    )): ''))
    	->cast();
}
