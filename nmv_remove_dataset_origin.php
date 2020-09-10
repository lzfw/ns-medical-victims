<?php

require_once 'zefiro/ini.php';
require_once 'zefiro/lib/opendb_mysql.php';
require_once 'zefiro/lib/url.php';
require_once 'nmv_remove_base.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Dataset Origin','nmv_list_dataset_origin');

function breadcrumb($dbi, $item, $record_id) {
    $dataset_origin_names = $item->work_group;
    $dbi->addBreadcrumb ($dataset_origin_names,'nmv_view_dataset_origin?ID_dataset_origin='.$record_id);
}

function prompt($dbi, $item) {
    $dataset_origin = htmlspecialchars($item->ID_dataset_origin, ENT_HTML5).': '.
        htmlspecialchars($item->work_group, ENT_HTML5);
    return 'Remove dataset origin'.': "<em>'.$dataset_origin.'</em>".';
}

remove_record('ID_dataset_origin', 'ID_dataset_origin', 'nmv__dataset_origin', 'dataset origin', 'nmv_list_dataset_origin', 'nmv_view_dataset_origin?ID_dataset_origin=', 'prompt', 'breadcrumb');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
