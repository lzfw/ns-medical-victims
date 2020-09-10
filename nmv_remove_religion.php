<?php

require_once 'zefiro/ini.php';
require_once 'zefiro/lib/opendb_mysql.php';
require_once 'zefiro/lib/url.php';
require_once 'nmv_remove_base.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Religion','nmv_list_religion');

function breadcrumb($dbi, $item, $record_id) {
    $religion_names = $item->english .
        ' (' . $item->deutsch . ')';
    $dbi->addBreadcrumb ($religion_names,'nmv_view_religion?ID_religion='.$record_id);
}

function prompt($dbi, $item) {
    $religion = htmlspecialchars($item->ID_religion, ENT_HTML5).': '.
        htmlspecialchars($item->english, ENT_HTML5).' ('.
        htmlspecialchars($item->deutsch, ENT_HTML5).')';
    return 'Remove religion'.': "<em>'.$religion.'</em>".';
}

remove_record('ID_religion', 'ID_religion', 'nmv__religion', 'religion', 'nmv_list_religion', 'nmv_view_religion?ID_religion=', 'prompt', 'breadcrumb');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
