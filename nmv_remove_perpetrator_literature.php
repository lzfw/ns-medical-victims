<?php

require_once 'zefiro/ini.php';
require_once 'zefiro/lib/opendb_mysql.php';
require_once 'zefiro/lib/url.php';
require_once 'nmv_remove_base.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Literature','nmv_list_literature');

function get_literature($dbi, $id) {
    $sql = "SELECT *
        FROM nmv__literature
        WHERE ID_literature = ?";

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

    $literature = $result->fetch_object();
    return $literature;
}

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

function breadcrumb($dbi, $item, $record_id) {
    $literature = get_literature($dbi, $item->ID_literature);
    $perpetrator = get_perpetrator($dbi, $item->ID_perpetrator);
    $perp_lit_name = $literature->lit_title . ' - ' . $perpetrator->first_names . ' ' . $perpetrator->surname;
    $dbi->addBreadcrumb ('Perpetrators','nmv_list_perpetrator_literature?ID_literature='.$item->ID_literature);
    $dbi->addBreadcrumb ($perp_lit_name,'nmv_view_perpetrator_literature?ID_literature='.$item->ID_literature);
}

function prompt($dbi, $item) {
    $literature = get_literature($dbi, $item->ID_literature);
    $perpetrator = get_perpetrator($dbi, $item->ID_perpetrator);

    $perpetrator_literature =
        htmlspecialchars($literature->lit_title, ENT_HTML5).' - ' . 
        htmlspecialchars($perpetrator->first_names, ENT_HTML5).' '.
        htmlspecialchars($perpetrator->surname, ENT_HTML5);
    return 'Remove Literature Perpetrator '.': "<em>'.$perpetrator_literature.'</em>".';
}

function redir($dbi, $type, $location, $item) {
    $literature = get_literature($dbi, $item->ID_literature);
    return "$location?ID_literature={$literature->ID_literature}";
}

remove_record('ID_perp_lit', 'ID_perp_lit', 'nmv__perpetrator_literature',
    'literature perpetrator', 'nmv_list_perpetrator_literature', 'nmv_list_perpetrator_literature',
    'prompt', 'breadcrumb', 'redir');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
