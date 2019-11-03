<?php

require_once 'zefiro/ini.php';
require_once 'zefiro/lib/opendb_mysql.php';
require_once 'zefiro/lib/url.php';
require_once 'nmv_remove_base.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Literature','nmv_list_literature');

function breadcrumb($dbi, $item, $record_id) {
    $literature_name = $item->lit_title;
    $dbi->addBreadcrumb ($literature_name,'nmv_view_literature?ID_literature='.$record_id);
}

function prompt($dbi, $item) {
    $literature = htmlspecialchars($item->ID_literature, ENT_HTML5).': '.
        '"' . htmlspecialchars($item->lit_title, ENT_HTML5).'" by '.
        htmlspecialchars($item->authors, ENT_HTML5);
    return 'Remove Literature'.': "<em>'.$literature.'</em>".';
}

remove_record('ID_literature', 'ID_literature', 'nmv__literature', 'literature', 'nmv_list_literature', 'nmv_view_literature?ID_literature=', 'prompt', 'breadcrumb');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
