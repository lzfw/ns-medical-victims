<?php

require_once 'zefiro/ini.php';
require_once 'zefiro/lib/opendb_mysql.php';
require_once 'zefiro/lib/url.php';
require_once 'nmv_remove_base.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Institution Type','nmv_list_institution_type');

function breadcrumb($dbi, $item, $record_id) {
    $institution_type_names = $item->english .
        ' (' . $item->deutsch . ')';
    $dbi->addBreadcrumb ($institution_type_names,'nmv_view_institution_type?ID_institution_type='.$record_id);
}

function prompt($dbi, $item) {
    $institution_type = htmlspecialchars($item->ID_institution_type, ENT_HTML5).': '.
        htmlspecialchars($item->english, ENT_HTML5).' ('.
        htmlspecialchars($item->deutsch, ENT_HTML5).')';
    return 'Remove institution type'.': "<em>'.$institution_type.'</em>".';
}

remove_record('ID_institution_type', 'ID_institution_type', 'nmv__institution_type', 'institution_type', 'nmv_list_institution_type', 'nmv_view_institution_type?ID_institution_type=', 'prompt', 'breadcrumb');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
