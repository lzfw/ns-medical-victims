<?php

require_once 'zefiro/ini.php';
require_once 'zefiro/lib/opendb_mysql.php';
require_once 'zefiro/lib/url.php';
require_once 'nmv_remove_base.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Status','nmv_list_victim_evaluation_status');

function breadcrumb($dbi, $item, $record_id) {
    $victim_evaluation_status_names = $item->english .
        ' (' . $item->deutsch . ')';
    $dbi->addBreadcrumb ($victim_evaluation_status_names,'nmv_view_victim_evaluation_status?ID_status='.$record_id);
}

function prompt($dbi, $item) {
    $victim_evaluation_status = htmlspecialchars($item->ID_status, ENT_HTML5).': '.
        htmlspecialchars($item->english, ENT_HTML5).' ('.
        htmlspecialchars($item->deutsch, ENT_HTML5).')';
    return 'Remove status'.': "<em>'.$victim_evaluation_status.'</em>".';
}

remove_record('ID_status', 'ID_status', 'nmv__victim_evaluation_status', 'status', 'nmv_list_victim_evaluation_status', 'nmv_view_victim_evaluation_status?ID_status=', 'prompt', 'breadcrumb');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
