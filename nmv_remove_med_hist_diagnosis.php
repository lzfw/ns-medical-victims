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

function get_med_hist_diagnosis($dbi, $id) {
    $sql = "SELECT d.ID_med_history_diagnosis id, d.year
        FROM nmv__med_history_diagnosis d
        WHERE ID_med_history_diagnosis= ?";

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

    $med_hist_hosp = $result->fetch_object();
    return $med_hist_diagnosis;
}

function breadcrumb($dbi, $item, $record_id) {
    $victim = get_victim($dbi, $item->ID_victim);
    $med_hist_diagnosis = get_med_hist_diagnosis($dbi, $item->ID_med_history_diagnosis);
    $med_hist_title = $med_hist_diagnosis->date . ' - ' . $victim->first_names . ' ' . $victim->surname;
    $dbi->addBreadcrumb ('Medical History','nmv_list_med_hist?ID_victim='.$item->ID_victim);
    $dbi->addBreadcrumb ($med_hist_title,'nmv_view_med_hist_diagnosis?ID_victim='.$item->ID_victim);
}

function prompt($dbi, $item) {
    $victim = get_victim($dbi, $item->ID_victim);
    $med_hist_hosp = get_med_hist_diagnosis($dbi, $item->ID_med_history_diagnosis);

    $victim_med_hist_diagnosis =
        htmlspecialchars($med_hist_diagnosis->year, ENT_HTML5).' - ' .
        htmlspecialchars($victim->first_names, ENT_HTML5).' '.
        htmlspecialchars($victim->surname, ENT_HTML5);
    return 'Medical History (Diagnosis)'.': "<em>'.$victim_med_hist_diagnosis.'</em>".';
}

function redir($dbi, $type, $location, $item) {
    $victim = get_victim($dbi, $item->ID_victim);
    return "$location?ID_victim={$victim->ID_victim}";
}

remove_record('ID_med_history_diagnosis', 'ID_med_history_diagnosis', 'nmv__med_history_diagnosis',
    'Medical History (Diagnosis)', 'nmv_list_med_hist', 'nmv_list_med_hist',
    'prompt', 'breadcrumb', 'redir');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
