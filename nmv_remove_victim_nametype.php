<?php

require_once 'zefiro/ini.php';
require_once 'zefiro/lib/opendb_mysql.php';
require_once 'zefiro/lib/url.php';
require_once 'nmv_remove_base.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Nametype','nmv_list_victim_nametype');

function breadcrumb($dbi, $item, $record_id) {
    $victim_nametype_names = $item->english .
        ' (' . $item->deutsch . ')';
    $dbi->addBreadcrumb ($victim_nametype_names,'nmv_view_victim_nametype?ID_nametype='.$record_id);
}

function prompt($dbi, $item) {
    $victim_nametype = htmlspecialchars($item->ID_nametype, ENT_HTML5).': '.
        htmlspecialchars($item->english, ENT_HTML5).' ('.
        htmlspecialchars($item->deutsch, ENT_HTML5).')';
    return 'Remove nametype'.': "<em>'.$victim_nametype.'</em>".';
}

remove_record('ID_nametype', 'ID_nametype', 'nmv__victim_nametype', 'nametype', 'nmv_list_victim_nametype', 'nmv_view_victim_nametype?ID_nametype=', 'prompt', 'breadcrumb');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
