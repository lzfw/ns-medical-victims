<?php

require_once 'zefiro/ini.php';
require_once 'zefiro/lib/opendb_mysql.php';
require_once 'zefiro/lib/url.php';
require_once 'nmv_remove_base.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Nationality','nmv_list_nationality');

function breadcrumb($dbi, $item, $record_id) {
    $nationality_names = $item->english .
        ' (' . $item->deutsch . ')';
    $dbi->addBreadcrumb ($nationality_names,'nmv_view_nationality?ID_nationality='.$record_id);
}

function prompt($dbi, $item) {
    $nationality = htmlspecialchars($item->ID_nationality, ENT_HTML5).': '.
        htmlspecialchars($item->english, ENT_HTML5).' ('.
        htmlspecialchars($item->deutsch, ENT_HTML5).')';
    return 'Remove nationality'.': "<em>'.$nationality.'</em>".';
}

remove_record('ID_nationality', 'ID_nationality', 'nmv__nationality', 'nationality', 'nmv_list_nationality', 'nmv_view_nationality?ID_nationality=', 'prompt', 'breadcrumb');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
