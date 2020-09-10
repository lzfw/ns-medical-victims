<?php

require_once 'zefiro/ini.php';
require_once 'zefiro/lib/opendb_mysql.php';
require_once 'zefiro/lib/url.php';
require_once 'nmv_remove_base.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Tissue Form','nmv_list_tissue_form');

function breadcrumb($dbi, $item, $record_id) {
    $tissue_form_names = $item->english .
        ' (' . $item->deutsch . ')';
    $dbi->addBreadcrumb ($tissue_form_names,'nmv_view_tissue_form?ID_tissue_form='.$record_id);
}

function prompt($dbi, $item) {
    $tissue_form = htmlspecialchars($item->ID_tissue_form, ENT_HTML5).': '.
        htmlspecialchars($item->english, ENT_HTML5).' ('.
        htmlspecialchars($item->deutsch, ENT_HTML5).')';
    return 'Remove tissue form'.': "<em>'.$tissue_form.'</em>".';
}

remove_record('ID_tissue_form', 'ID_tissue_form', 'nmv__tissue_form', 'tissue form', 'nmv_list_tissue_form', 'nmv_view_tissue_form?ID_tissue_form=', 'prompt', 'breadcrumb');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
