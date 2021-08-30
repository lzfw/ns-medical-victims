<?php

require_once 'zefiro/ini.php';
require_once 'zefiro/lib/opendb_mysql.php';
require_once 'zefiro/lib/url.php';
require_once 'nmv_remove_base.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Institutions','nmv_list_institutions');

function breadcrumb($dbi, $item, $record_id) {
    $institution_name = $item->institution_name .
        ' (' . $item->location . ')';
    $dbi->addBreadcrumb ($institution_name,'nmv_view_institution?ID_institution='.$record_id);
}

function prompt($dbi, $item) {
    $institution = htmlspecialchars($item->ID_institution, ENT_HTML5).': '.
        htmlspecialchars($item->institution_name, ENT_HTML5).' '.
        htmlspecialchars($item->location, ENT_HTML5);
    return 'Remove Institution'.': "<em>'.$institution.'</em>".';
}

remove_record('ID_institution', 'ID_institution', 'nmv__institution', 'institution', 'nmv_list_institutions', 'nmv_view_institution?ID_institution=', 'prompt', 'breadcrumb');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
