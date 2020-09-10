<?php

require_once 'zefiro/ini.php';
require_once 'zefiro/lib/opendb_mysql.php';
require_once 'zefiro/lib/url.php';
require_once 'nmv_remove_base.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Disability','nmv_list_disability');

function breadcrumb($dbi, $item, $record_id) {
    $disability_names = $item->english .
        ' (' . $item->deutsch . ')';
    $dbi->addBreadcrumb ($disability_names,'nmv_view_disability?ID_disability='.$record_id);
}

function prompt($dbi, $item) {
    $disability = htmlspecialchars($item->ID_disability, ENT_HTML5).': '.
        htmlspecialchars($item->english, ENT_HTML5).' ('.
        htmlspecialchars($item->deutsch, ENT_HTML5).')';
    return 'Remove disability'.': "<em>'.$disability.'</em>".';
}

remove_record('ID_disability', 'ID_disability', 'nmv__disability', 'disability', 'nmv_list_disability', 'nmv_view_disability?ID_disability=', 'prompt', 'breadcrumb');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
