<?php

require_once 'zefiro/ini.php';
require_once 'zefiro/lib/opendb_mysql.php';
require_once 'zefiro/lib/url.php';
require_once 'nmv_remove_base.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Prisoner Assistants','nmv_list_prisoner_assistants');

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
    $victim = get_victim($dbi, $item->ID_victim);
    $experiment = get_experiment($dbi, $item->ID_experiment);
    $vict_exp_name = $experiment->experiment_title . ' - ' . $victim->first_names . ' ' . $victim->surname;
    $dbi->addBreadcrumb ('Biomedical Research','nmv_list_victim_experiment?ID_victim='.$item->ID_victim);
    $dbi->addBreadcrumb ($vict_exp_name,'nmv_view_prisoner_assistant_experiment?ID_victim='.$item->ID_victim);
}

function prompt($dbi, $item) {
    $victim = get_victim($dbi, $item->ID_victim);
    $experiment = get_experiment($dbi, $item->ID_experiment);

    $victim_experiment =
        htmlspecialchars($experiment->experiment_title, ENT_HTML5).' - ' .
        htmlspecialchars($victim->first_names, ENT_HTML5).' '.
        htmlspecialchars($victim->surname, ENT_HTML5);
    return 'Remove link between biomedical research and prisoner assistant '.': "<em>'.$victim_experiment.'</em>".';
}

function redir($dbi, $type, $location, $item) {
    $victim = get_victim($dbi, $item->ID_victim);
    return "$location?ID_victim={$victim->ID_victim}";
}

remove_record('ID_pa_exp', 'ID_pa_exp', 'nmv__prisoner_assistant_experiment',
    'Prisoner Assistant Biomedical Research', 'nmv_list_victim_experiment', 'nmv_list_victim_experiment',
    'prompt', 'breadcrumb', 'redir');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
