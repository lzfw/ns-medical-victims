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

function get_experiment($dbi, $id) {
    $sql = "SELECT *
        FROM nmv__experiment
        WHERE ID_experiment = ?";

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

    $experiment = $result->fetch_object();
    return $experiment;
}

function breadcrumb($dbi, $item, $record_id) {
    $literature = get_literature($dbi, $item->ID_literature);
    $experiment = get_experiment($dbi, $item->ID_experiment);
    $exp_lit_name = $experiment->experiment_title . ' - ' . $literature->lit_title . ' (' . $literature->authors . ')';
    $dbi->addBreadcrumb ('Biomedical Research','nmv_list_experiment_literature?ID_literature='.$item->ID_literature);
    $dbi->addBreadcrumb ($exp_lit_name,'nmv_view_experiment_literature?ID_literature='.$item->ID_literature);
}

function prompt($dbi, $item) {
    $literature = get_literature($dbi, $item->ID_literature);
    $experiment = get_experiment($dbi, $item->ID_experiment);

    $experiment_literature =
        htmlspecialchars($experiment->experiment_title, ENT_HTML5).' - ' . 
        htmlspecialchars($literature->lit_title, ENT_HTML5).' ('.
        htmlspecialchars($literature->authors, ENT_HTML5) . ')';
    return 'Remove biomedical research literature'.': "<em>'.$experiment_literature.'</em>".';
}

function redir($dbi, $type, $location, $item) {
    $literature = get_literature($dbi, $item->ID_literature);
    return "$location?ID_literature={$literature->ID_literature}";
}

remove_record('ID_exp_lit', 'ID_exp_lit', 'nmv__experiment_literature',
    'Biomedical Research Literature', 'nmv_list_experiment_literature', 'nmv_list_experiment_literature',
    'prompt', 'breadcrumb', 'redir');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
