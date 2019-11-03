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

function get_med_hist_tissue($dbi, $id) {
    $sql = "SELECT h.ID_med_history_tissue id, f.english tissue_form,
            s.english tissue_state, location,
            CONCAT_WS('-', h.since_year, h.since_month, h.since_day) date
        FROM nmv__med_history_tissue h
        LEFT JOIN nmv__tissue_form f ON f.ID_tissue_form = h.ID_tissue_form
        LEFT JOIN nmv__tissue_state s ON s.ID_tissue_state = h.ID_tissue_state
        WHERE ID_med_history_tissue = ?";

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

    $med_hist_tissue = $result->fetch_object();
    return $med_hist_tissue;
}

function breadcrumb($dbi, $item, $record_id) {
    $victim = get_victim($dbi, $item->ID_victim);
    $med_hist_tissue = get_med_hist_tissue($dbi, $item->ID_med_history_tissue);
    $med_hist_title = $med_hist_tissue->date . ' - ' . $victim->first_names . ' ' . $victim->surname;
    $dbi->addBreadcrumb ('Medical History','nmv_list_med_hist?ID_victim='.$item->ID_victim);
    $dbi->addBreadcrumb ($med_hist_title,'nmv_view_med_hist_tissue?ID_victim='.$item->ID_victim);
}

function prompt($dbi, $item) {
    $victim = get_victim($dbi, $item->ID_victim);
    $med_hist_tissue = get_med_hist_tissue($dbi, $item->ID_med_history_tissue);

    $victim_med_hist_tissue =
        htmlspecialchars($med_hist_tissue->tissue_form, ENT_HTML5).' - ' .
        htmlspecialchars($med_hist_tissue->tissue_state, ENT_HTML5).' - ' .
        htmlspecialchars($med_hist_tissue->location, ENT_HTML5).' - ' .
        htmlspecialchars($med_hist_tissue->date, ENT_HTML5).' - ' .
        htmlspecialchars($victim->first_names, ENT_HTML5).' '.
        htmlspecialchars($victim->surname, ENT_HTML5);
    return 'Medical History (Tissue)'.': "<em>'.$victim_med_hist_tissue.'</em>".';
}

function redir($dbi, $type, $location, $item) {
    $victim = get_victim($dbi, $item->ID_victim);
    return "$location?ID_victim={$victim->ID_victim}";
}

remove_record('ID_med_history_tissue', 'ID_med_history_tissue', 'nmv__med_history_tissue',
    'Medical History (Tissue)', 'nmv_list_med_hist', 'nmv_list_med_hist',
    'prompt', 'breadcrumb', 'redir');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
