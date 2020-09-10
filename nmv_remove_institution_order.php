<?php

require_once 'zefiro/ini.php';
require_once 'zefiro/lib/opendb_mysql.php';
require_once 'zefiro/lib/url.php';
require_once 'nmv_remove_base.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Institution Order','nmv_list_institution_order');

function breadcrumb($dbi, $item, $record_id) {
    $institution_order_names = $item->english .
        ' (' . $item->deutsch . ')';
    $dbi->addBreadcrumb ($institution_order_names,'nmv_view_institution_order?ID_institution_order='.$record_id);
}

function prompt($dbi, $item) {
    $institution_order = htmlspecialchars($item->ID_institution_order, ENT_HTML5).': '.
        htmlspecialchars($item->english, ENT_HTML5).' ('.
        htmlspecialchars($item->deutsch, ENT_HTML5).')';
    return 'Remove Institution Order'.': "<em>'.$institution_order.'</em>".';
}

remove_record('ID_institution_order', 'ID_institution_order', 'nmv__institution_order', 'institution order', 'nmv_list_institution_order', 'nmv_view_institution_order?ID_institution_order=', 'prompt', 'breadcrumb');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
