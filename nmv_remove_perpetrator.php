<?php

require_once 'zefiro/ini.php';
require_once 'zefiro/lib/opendb_mysql.php';
require_once 'zefiro/lib/url.php';
require_once 'nmv_remove_base.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Perpetrators','nmv_list_perpetrators');

function breadcrumb($dbi, $item, $record_id) {
    $perpetrator_name = $item->first_names . ' ' . $item->surname;
    $dbi->addBreadcrumb ($perpetrator_name,'nmv_view_perpetrator?ID_perpetrator='.$record_id);
}

function prompt($dbi, $item) {
    $perpetrator = htmlspecialchars($item->ID_perpetrator, ENT_HTML5).': '.
        htmlspecialchars($item->first_names, ENT_HTML5).' '.
        htmlspecialchars($item->surname, ENT_HTML5);
    return 'Remove Perpetrator'.': "<em>'.$perpetrator.'</em>".';
}

remove_record('ID_perpetrator', 'ID_perpetrator', 'nmv__perpetrator', 'perpetrator', 'nmv_list_perpetrators', 'nmv_view_perpetrator?ID_perpetrator=', 'prompt', 'breadcrumb');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
