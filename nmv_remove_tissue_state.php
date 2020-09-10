<?php

require_once 'zefiro/ini.php';
require_once 'zefiro/lib/opendb_mysql.php';
require_once 'zefiro/lib/url.php';
require_once 'nmv_remove_base.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Tissue State','nmv_list_tissue_state');

function breadcrumb($dbi, $item, $record_id) {
    $tissue_state_names = $item->english .
        ' (' . $item->deutsch . ')';
    $dbi->addBreadcrumb ($tissue_state_names,'nmv_view_tissue_state?ID_tissue_state='.$record_id);
}

function prompt($dbi, $item) {
    $tissue_state = htmlspecialchars($item->ID_tissue_state, ENT_HTML5).': '.
        htmlspecialchars($item->english, ENT_HTML5).' ('.
        htmlspecialchars($item->deutsch, ENT_HTML5).')';
    return 'Remove tissue state'.': "<em>'.$tissue_state.'</em>".';
}

remove_record('ID_tissue_state', 'ID_tissue_state', 'nmv__tissue_state', 'tissue state', 'nmv_list_tissue_state', 'nmv_view_tissue_state?ID_tissue_state=', 'prompt', 'breadcrumb');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
