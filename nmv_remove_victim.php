<?php

require_once 'zefiro/ini.php';
require_once 'zefiro/lib/opendb_mysql.php';
require_once 'zefiro/lib/url.php';
require_once 'nmv_remove_base.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Victims','nmv_list_victims');

function breadcrumb($dbi, $item, $record_id) {
    $victim_name = $item->first_names . ' ' . $item->surname;
    $dbi->addBreadcrumb ($victim_name,'nmv_view_victim?ID_victim='.$record_id);
}

function prompt($dbi, $item) {
    $victim = htmlspecialchars($item->ID_victim, ENT_HTML5).': '.
        htmlspecialchars($item->first_names, ENT_HTML5).' '.
        htmlspecialchars($item->surname, ENT_HTML5);
    return 'Remove Victim'.': "<em>'.$victim.'</em>".';
}

remove_record('ID_victim', 'ID_victim', 'nmv__victim', 'victim', 'nmv_list_victims', 'nmv_view_victim?ID_victim=', 'prompt', 'breadcrumb');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
