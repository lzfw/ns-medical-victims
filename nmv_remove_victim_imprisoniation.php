<?php

require_once 'zefiro/ini.php';
require_once 'zefiro/lib/opendb_mysql.php';
require_once 'zefiro/lib/url.php';
require_once 'nmv_remove_base.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Victims','nmv_list_victims');

function get_victim($dbi, $id) {
    $sql = "SELECT *
        FROM nmv__victim
        WHERE ID_victim = ?";

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

    $victim = $result->fetch_object();
    return $victim;
}

function get_imprisoniation($dbi, $id) {
    $sql = "SELECT *
        FROM nmv__imprisoniation
        WHERE ID_imprisoniation = ?";

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

    $imprisoniation = $result->fetch_object();
    return $imprisoniation;
}

function breadcrumb($dbi, $item, $record_id) {
    $victim = get_victim($dbi, $item->ID_victim);
    $imprisoniation = get_imprisoniation($dbi, $item->ID_imprisoniation);
    $vict_name = $victim->first_names . ' ' . $victim->surname;
    $vict_name = strlen($vict_name) > 1 ? $vict_name : 'unknown';
    $dbi->addBreadcrumb ($vict_name,'nmv_view_victim?ID_victim='.$item->ID_victim);
}

function prompt($dbi, $item) {
    $victim = get_victim($dbi, $item->ID_victim);
    $imprisoniation = get_imprisoniation($dbi, $item->ID_imprisoniation);

    $victim_source =
        htmlspecialchars($imprisoniation->location, ENT_HTML5).', ' .
        htmlspecialchars($imprisoniation->number, ENT_HTML5).' - ' .
        htmlspecialchars($victim->first_names, ENT_HTML5).' '.
        htmlspecialchars($victim->surname, ENT_HTML5);
    return 'Remove Victim Classification (prison number) '.': "<em>'.$victim_source.'</em>".';
}

function redir($dbi, $type, $location, $item) {
    $victim = get_victim($dbi, $item->ID_victim);
    return "$location?ID_victim={$victim->ID_victim}";
}

remove_record('ID_imprisoniation', 'ID_imprisoniation', 'nmv__imprisoniation',
    'Victim Classification', 'nmv_view_victim', 'nmv_view_victim',
    'prompt', 'breadcrumb', 'redir');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
