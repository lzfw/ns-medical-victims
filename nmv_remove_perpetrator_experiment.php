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
    $perpetrator = get_perpetrator($dbi, $item->ID_perpetrator);
    $experiment = get_experiment($dbi, $item->ID_experiment);
    $perp_exp_name = $experiment->experiment_title . ' - ' . $perpetrator->first_names . ' ' . $perpetrator->surname;
    $dbi->addBreadcrumb ('Biomedical Research','nmv_list_perpetrator_experiment?ID_perpetrator='.$item->ID_perpetrator);
    $dbi->addBreadcrumb ($perp_exp_name,'nmv_view_perpetrator_experiment?ID_perpetrator='.$item->ID_perpetrator);
}

function prompt($dbi, $item) {
    $perpetrator = get_perpetrator($dbi, $item->ID_perpetrator);
    $experiment = get_experiment($dbi, $item->ID_experiment);

    $perpetrator_experiment =
        htmlspecialchars($experiment->experiment_title, ENT_HTML5).' - ' . 
        htmlspecialchars($perpetrator->first_names, ENT_HTML5).' '.
        htmlspecialchars($perpetrator->surname, ENT_HTML5);
    return 'Remove perpetrator biomedical research '.': "<em>'.$perpetrator_experiment.'</em>".';
}

function redir($dbi, $type, $location, $item) {
    $perpetrator = get_perpetrator($dbi, $item->ID_perpetrator);
    return "$location?ID_perpetrator={$perpetrator->ID_perpetrator}";
}

remove_record('ID_perp_exp', 'ID_perp_exp', 'nmv__perpetrator_experiment',
    'Perpetrator Biomedical Research', 'nmv_list_perpetrator_experiment', 'nmv_list_perpetrator_experiment',
    'prompt', 'breadcrumb', 'redir');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
