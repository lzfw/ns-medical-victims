<?php

require_once 'zefiro/ini.php';
require_once 'zefiro/lib/opendb_mysql.php';
require_once 'zefiro/lib/url.php';
require_once 'nmv_remove_base.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Education','nmv_list_education');

function breadcrumb($dbi, $item, $record_id) {
    $education_names = $item->english .
        ' (' . $item->deutsch . ')';
    $dbi->addBreadcrumb ($education_names,'nmv_view_education?ID_education='.$record_id);
}

function prompt($dbi, $item) {
    $education = htmlspecialchars($item->ID_education, ENT_HTML5).': '.
        htmlspecialchars($item->english, ENT_HTML5).' ('.
        htmlspecialchars($item->deutsch, ENT_HTML5).')';
    return 'Remove education'.': "<em>'.$education.'</em>".';
}

remove_record('ID_education', 'ID_education', 'nmv__education', 'education', 'nmv_list_education', 'nmv_view_education?ID_education=', 'prompt', 'breadcrumb');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
