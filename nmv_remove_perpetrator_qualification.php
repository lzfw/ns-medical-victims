<?php

require_once 'zefiro/ini.php';
require_once 'zefiro/lib/opendb_mysql.php';
require_once 'zefiro/lib/url.php';
require_once 'nmv_remove_base.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('perpetrators','nmv_list_perpetrators');

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

function get_qualification($dbi, $id) {
    $sql = "SELECT *
        FROM nmv__qualification
        WHERE ID_qualification = ?";

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

    $qualification = $result->fetch_object();
    return $qualification;
}

function breadcrumb($dbi, $item, $record_id) {
    $perpetrator = get_perpetrator($dbi, $item->ID_perpetrator);
    $qualification = get_qualification($dbi, $item->ID_qualification);
    $perp_name = $perpetrator->first_names . ' ' . $perpetrator->surname;
    $perp_name = strlen($perp_name) > 1 ? $perp_name : 'unknown';
    $dbi->addBreadcrumb ($perp_name,'nmv_view_perpetrator?ID_perpetrator='.$item->ID_perpetrator);
}

function prompt($dbi, $item) {
    $perpetrator = get_perpetrator($dbi, $item->ID_perpetrator);
    $qualification = get_qualification($dbi, $item->ID_qualification);

    $perpetrator_source =
        htmlspecialchars($qualification->qualification_year, ENT_HTML5).', ' .
        htmlspecialchars($qualification->qualification_place, ENT_HTML5).', ' .
        htmlspecialchars($qualification->qualification_type, ENT_HTML5).', '.
        htmlspecialchars($qualification->thesis_title, ENT_HTML5);
    return 'Remove perpetrator qualification '.': "<em>'.$perpetrator_source.'</em>".';
}

function redir($dbi, $type, $location, $item) {
    $perpetrator = get_perpetrator($dbi, $item->ID_perpetrator);
    return "$location?ID_perpetrator={$perpetrator->ID_perpetrator}";
}

remove_record('ID_qualification', 'ID_qualification', 'nmv__qualification',
    'perpetrator qualification', 'nmv_view_perpetrator', 'nmv_view_perpetrator',
    'prompt', 'breadcrumb', 'redir');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
