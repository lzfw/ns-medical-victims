<?php

require_once 'zefiro/ini.php';
require_once 'zefiro/lib/opendb_mysql.php';
require_once 'zefiro/lib/url.php';
require_once 'nmv_remove_base.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Perpetrator Classification','nmv_list_perpetrator_classification');

function breadcrumb($dbi, $item, $record_id) {
    $classification_names = $item->english .
        ' (' . $item->deutsch . ')';
    $dbi->addBreadcrumb ($classification_names,'nmv_view_perpetrator_classification?ID_perp_class='.$record_id);
}

function prompt($dbi, $item) {
    $classification = htmlspecialchars($item->ID_perp_class, ENT_HTML5).': '.
        htmlspecialchars($item->english, ENT_HTML5).' ('.
        htmlspecialchars($item->deutsch, ENT_HTML5).')';
    return 'Remove perpetrator classification'.': "<em>'.$classification.'</em>".';
}

remove_record('ID_perp_class', 'ID_perp_class', 'nmv__perpetrator_classification', 'perpetrator classification', 'nmv_list_perpetrator_classification', 'nmv_view_perpetrator_classification?ID_perp_class=', 'prompt', 'breadcrumb');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
