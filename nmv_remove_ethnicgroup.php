<?php

require_once 'zefiro/ini.php';
require_once 'zefiro/lib/opendb_mysql.php';
require_once 'zefiro/lib/url.php';
require_once 'nmv_remove_base.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Ethnicgroup','nmv_list_ethnicgroup');

function breadcrumb($dbi, $item, $record_id) {
    $ethnicgroup_names = $item->english .
        ' (' . $item->deutsch . ')';
    $dbi->addBreadcrumb ($ethnicgroup_names,'nmv_view_ethnicgroup?ID_ethnicgroup='.$record_id);
}

function prompt($dbi, $item) {
    $ethnicgroup = htmlspecialchars($item->ID_ethnicgroup, ENT_HTML5).': '.
        htmlspecialchars($item->english, ENT_HTML5).' ('.
        htmlspecialchars($item->deutsch, ENT_HTML5).')';
    return 'Remove ethnicgroup'.': "<em>'.$ethnicgroup.'</em>".';
}

remove_record('ID_ethnicgroup', 'ID_ethnicgroup', 'nmv__ethnicgroup', 'ethnicgroup', 'nmv_list_ethnicgroup', 'nmv_view_ethnicgroup?ID_ethnicgroup=', 'prompt', 'breadcrumb');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
