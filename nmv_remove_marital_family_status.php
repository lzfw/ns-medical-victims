<?php

require_once 'zefiro/ini.php';
require_once 'zefiro/lib/opendb_mysql.php';
require_once 'zefiro/lib/url.php';
require_once 'nmv_remove_base.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Marital / Family Status','nmv_list_marital_family_status');

function breadcrumb($dbi, $item, $record_id) {
    $marital_family_status_names = $item->english .
        ' (' . $item->deutsch . ')';
    $dbi->addBreadcrumb ($marital_family_status_names,'nmv_view_marital_family_status?ID_marital_family_status='.$record_id);
}

function prompt($dbi, $item) {
    $marital_family_status = htmlspecialchars($item->ID_marital_family_status, ENT_HTML5).': '.
        htmlspecialchars($item->english, ENT_HTML5).' ('.
        htmlspecialchars($item->deutsch, ENT_HTML5).')';
    return 'Remove marital / family status'.': "<em>'.$marital_family_status.'</em>".';
}

remove_record('ID_marital_family_status', 'ID_marital_family_status', 'nmv__marital_family_status', 'Marital / Family Status', 'nmv_list_marital_family_status', 'nmv_view_marital_family_status?ID_marital_family_status=', 'prompt', 'breadcrumb');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
