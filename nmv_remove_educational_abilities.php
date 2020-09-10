<?php

require_once 'zefiro/ini.php';
require_once 'zefiro/lib/opendb_mysql.php';
require_once 'zefiro/lib/url.php';
require_once 'nmv_remove_base.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Educational Ablilities','nmv_list_educational_abilities');

function breadcrumb($dbi, $item, $record_id) {
    $educational_abilities_names = $item->english .
        ' (' . $item->deutsch . ')';
    $dbi->addBreadcrumb ($educational_abilities_names,'nmv_view_educational_abilities?ID_educational_abilities='.$record_id);
}

function prompt($dbi, $item) {
    $educational_abilities = htmlspecialchars($item->ID_educational_abilities, ENT_HTML5).': '.
        htmlspecialchars($item->english, ENT_HTML5).' ('.
        htmlspecialchars($item->deutsch, ENT_HTML5).')';
    return 'Remove educational ability'.': "<em>'.$educational_abilities.'</em>".';
}

remove_record('ID_educational_abilities', 'ID_educational_abilities', 'nmv__educational_abilities', 'educational abilities', 'nmv_list_educational_abilities', 'nmv_view_educational_abilities?ID_educational_abilities=', 'prompt', 'breadcrumb');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
