<?php

require_once 'zefiro/ini.php';
require_once 'zefiro/lib/opendb_mysql.php';
require_once 'zefiro/lib/url.php';
require_once 'nmv_remove_base.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Perpetrators','nmv_list_perpetrators');

function get_perpetrator($dbi, $id) {
    $sql = "SELECT *
        FROM nmv__perpetrator
        WHERE ID_perpetrator = ?";

    if ($stmt = $dbi->connection->prepare($sql)) {
        if ( $stmt->bind_param('i', $id) ) {
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

    $perpetrator = $result->fetch_object();
    return $perpetrator;
}

function get_authority_record($dbi, $id) {
    $sql = "SELECT *
        FROM nmv__authority_record_perpetrator
        WHERE ID_authority_record_perpetrator = ?";

    if ($stmt = $dbi->connection->prepare($sql)) {
        if ( $stmt->bind_param('i', $id) ) {
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

    $authority_record = $result->fetch_object();
    return $authority_record;
}

function breadcrumb($dbi, $item, $record_id) {
    $perpetrator = get_perpetrator($dbi, $item->ID_perpetrator);
    $perpetrator_name = $perpetrator->first_names . ' ' . $perpetrator->surname;
    $perpetrator_name = strlen($perpetrator_name) > 1 ? $perpetrator_name : 'unknown';
    $dbi->addBreadcrumb ($perpetrator_name,'nmv_view_perpetrator?ID_perpetrator='.$item->ID_perpetrator);
}

function prompt($dbi, $item) {
    $authority_record = get_authority_record($dbi, $item->ID_authority_record_perpetrator);

    $perpetrator_source =
        htmlspecialchars($authority_record->authority_type, ENT_HTML5).' -- ' .
        htmlspecialchars($authority_record->authority_id, ENT_HTML5);
    return 'Remove Authority Record '.': "<em>'.$perpetrator_source.'</em>".';
}

function redir($dbi, $type, $location, $item) {
    $perpetrator = get_perpetrator($dbi, $item->ID_perpetrator);
    return "$location?ID_perpetrator={$perpetrator->ID_perpetrator}";
}

remove_record('ID_authority_record_perpetrator', 'ID_authority_record_perpetrator', 'nmv__authority_record_perpetrator',
    'authority record', 'nmv_view_perpetrator', 'nmv_view_perpetrator',
    'prompt', 'breadcrumb', 'redir');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
