<?php

require_once 'zefiro/ini.php';
require_once 'zefiro/lib/opendb_mysql.php';
require_once 'zefiro/lib/url.php';
require_once 'nmv_remove_base.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Diagnosis','nmv_list_diagnosis');

function breadcrumb($dbi, $item, $record_id) {
    $diagnosis_names = $item->english .
        ' (' . $item->type . ')';
    $dbi->addBreadcrumb ($diagnosis_names,'nmv_view_diagnosis?ID_diagnosis='.$record_id);
}

function prompt($dbi, $item) {
    $diagnosis = htmlspecialchars($item->ID_diagnosis, ENT_HTML5).': '.
        htmlspecialchars($item->english, ENT_HTML5).' ('.
        htmlspecialchars($item->type, ENT_HTML5).')';
    return 'Remove diagnosis'.': "<em>'.$diagnosis.'</em>".';
}

remove_record('ID_diagnosis', 'ID_diagnosis', 'nmv__diagnosis', 'diagnosis', 'nmv_list_diagnosis', 'nmv_view_diagnosis?ID_diagnosis=', 'prompt', 'breadcrumb');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
