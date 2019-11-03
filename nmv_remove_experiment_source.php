<?php

require_once 'zefiro/ini.php';
require_once 'zefiro/lib/opendb_mysql.php';
require_once 'zefiro/lib/url.php';
require_once 'nmv_remove_base.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Sources','nmv_list_sources');

function get_source($dbi, $id) {
    $sql = "SELECT *
        FROM nmv__source
        WHERE ID_source = ?";

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

    $source = $result->fetch_object();
    return $source;
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
    $source = get_source($dbi, $item->ID_source);
    $experiment = get_experiment($dbi, $item->ID_experiment);
    $exp_source_name = $experiment->experiment_title . ' - ' . $source->source_title . ' ' . $source->creation_year;
    $dbi->addBreadcrumb ('Biomedical Research','nmv_list_experiment_source?ID_source='.$item->ID_source);
    $dbi->addBreadcrumb ($exp_source_name,'nmv_view_experiment_source?ID_source='.$item->ID_source);
}

function prompt($dbi, $item) {
    $source = get_source($dbi, $item->ID_source);
    $experiment = get_experiment($dbi, $item->ID_experiment);

    $experiment_source =
        htmlspecialchars($experiment->experiment_title, ENT_HTML5).' - ' . 
        htmlspecialchars($source->source_title, ENT_HTML5).' '.
        htmlspecialchars($source->creation_year, ENT_HTML5);
    return 'Remove biomedical research source'.': "<em>'.$experiment_source.'</em>".';
}

function redir($dbi, $type, $location, $item) {
    $source = get_source($dbi, $item->ID_source);
    return "$location?ID_source={$source->ID_source}";
}

remove_record('ID_exp_source', 'ID_exp_source', 'nmv__experiment_source',
    'Biomedical Research Source', 'nmv_list_experiment_source', 'nmv_list_experiment_source',
    'prompt', 'breadcrumb', 'redir');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
