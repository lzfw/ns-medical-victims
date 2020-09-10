<?php

require_once 'zefiro/ini.php';
require_once 'zefiro/lib/opendb_mysql.php';
require_once 'zefiro/lib/url.php';
require_once 'nmv_remove_base.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Survival','nmv_list_survival');

function breadcrumb($dbi, $item, $record_id) {
    $survival_names = $item->english .
        ' (' . $item->deutsch . ')';
    $dbi->addBreadcrumb ($survival_names,'nmv_view_survival?ID_survival='.$record_id);
}

function prompt($dbi, $item) {
    $survival = htmlspecialchars($item->ID_survival, ENT_HTML5).': '.
        htmlspecialchars($item->english, ENT_HTML5).' ('.
        htmlspecialchars($item->deutsch, ENT_HTML5).')';
    return 'Remove Survival'.': "<em>'.$survival.'</em>".';
}

remove_record('ID_survival', 'ID_survival', 'nmv__survival', 'survival', 'nmv_list_survival', 'nmv_view_survival?ID_survival=', 'prompt', 'breadcrumb');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
