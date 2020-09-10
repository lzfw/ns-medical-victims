<?php

require_once 'zefiro/ini.php';
require_once 'zefiro/lib/opendb_mysql.php';
require_once 'zefiro/lib/url.php';
require_once 'nmv_remove_base.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Compensation','nmv_list_victim_evaluation_compensation');

function breadcrumb($dbi, $item, $record_id) {
    $victim_evaluation_compensation_names = $item->english .
        ' (' . $item->deutsch . ')';
    $dbi->addBreadcrumb ($victim_evaluation_compensation_names,'nmv_view_victim_evaluation_compensation?ID_compensation='.$record_id);
}

function prompt($dbi, $item) {
    $victim_evaluation_compensation = htmlspecialchars($item->ID_compensation, ENT_HTML5).': '.
        htmlspecialchars($item->english, ENT_HTML5).' ('.
        htmlspecialchars($item->deutsch, ENT_HTML5).')';
    return 'Remove compensation'.': "<em>'.$victim_evaluation_compensation.'</em>".';
}

remove_record('ID_compensation', 'ID_compensation', 'nmv__victim_evaluation_compensation', 'compensation', 'nmv_list_victim_evaluation_compensation', 'nmv_view_victim_evaluation_compensation?ID_compensation=', 'prompt', 'breadcrumb');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
