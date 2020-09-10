<?php

require_once 'zefiro/ini.php';
require_once 'zefiro/lib/opendb_mysql.php';
require_once 'zefiro/lib/url.php';
require_once 'nmv_remove_base.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Occupation','nmv_list_occupation');

function breadcrumb($dbi, $item, $record_id) {
    $occupation_names = $item->english .
        ' (' . $item->deutsch . ')';
    $dbi->addBreadcrumb ($occupation_names,'nmv_view_occupation?ID_occupation='.$record_id);
}

function prompt($dbi, $item) {
    $occupation = htmlspecialchars($item->ID_occupation, ENT_HTML5).': '.
        htmlspecialchars($item->english, ENT_HTML5).' ('.
        htmlspecialchars($item->deutsch, ENT_HTML5).')';
    return 'Remove occupation'.': "<em>'.$occupation.'</em>".';
}

remove_record('ID_occupation', 'ID_occupation', 'nmv__occupation', 'occupation', 'nmv_list_occupation', 'nmv_view_occupation?ID_occupation=', 'prompt', 'breadcrumb');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
