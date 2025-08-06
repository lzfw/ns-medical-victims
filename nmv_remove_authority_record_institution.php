<?php

require_once 'zefiro/ini.php';
require_once 'zefiro/lib/opendb_mysql.php';
require_once 'zefiro/lib/url.php';
require_once 'nmv_remove_base.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Institutions','nmv_list_institutions');

function get_institution($dbi, $id) {
    $sql = "SELECT *
        FROM nmv__institution
        WHERE ID_institution = ?";

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

    $institution = $result->fetch_object();
    return $institution;
}

function get_authority_record($dbi, $id) {
    $sql = "SELECT *
        FROM nmv__authority_record_institution
        WHERE ID_authority_record_institution = ?";

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
    $institution = get_institution($dbi, $item->ID_institution);
    $institution_name = $institution->institution_name;
    $institution_name = strlen($institution_name) > 1 ? $institution_name : 'unknown';
    $dbi->addBreadcrumb ($institution_name,'nmv_view_institution?ID_institution='.$item->ID_institution);
}

function prompt($dbi, $item) {
    $authority_record = get_authority_record($dbi, $item->ID_authority_record_institution);

    $institution_source =
        htmlspecialchars($authority_record->authority_type, ENT_HTML5).'  --  ' .
        htmlspecialchars($authority_record->authority_id, ENT_HTML5);
    return 'Remove Authority Record '.': "<em>'.$institution_source.'</em>".';
}

function redir($dbi, $type, $location, $item) {
    $institution = get_institution($dbi, $item->ID_institution);
    return "$location?ID_institution={$institution->ID_institution}";
}

remove_record('ID_authority_record_institution', 'ID_authority_record_institution', 'nmv__authority_record_institution',
    'authority record', 'nmv_view_institution', 'nmv_view_institution',
    'prompt', 'breadcrumb', 'redir');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
