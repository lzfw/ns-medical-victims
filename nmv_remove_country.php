<?php

require_once 'zefiro/ini.php';
require_once 'zefiro/lib/opendb_mysql.php';
require_once 'zefiro/lib/url.php';
require_once 'nmv_remove_base.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Country','nmv_list_country');

function breadcrumb($dbi, $item, $record_id) {
    $country_names = $item->english .
        ' (' . $item->local_name . ')';
    $dbi->addBreadcrumb ($country_names,'nmv_view_country?ID_country='.$record_id);
}

function prompt($dbi, $item) {
    $country = htmlspecialchars($item->ID_country, ENT_HTML5).': '.
        htmlspecialchars($item->english, ENT_HTML5).' ('.
        htmlspecialchars($item->local_name, ENT_HTML5).')';
    return 'Remove country'.': "<em>'.$country.'</em>".';
}

remove_record('ID_country', 'ID_country', 'nmv__country', 'country', 'nmv_list_country', 'nmv_view_country?ID_country=', 'prompt', 'breadcrumb');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
