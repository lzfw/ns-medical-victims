<?php

require_once 'zefiro/ini.php';
require_once 'zefiro/lib/opendb_mysql.php';
require_once 'zefiro/lib/url.php';
require_once 'nmv_remove_base.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Language','nmv_list_language');

function breadcrumb($dbi, $item, $record_id) {
    $language_names = $item->english .
        ' (' . $item->deutsch . ')';
    $dbi->addBreadcrumb ($language_names,'nmv_view_language?ID_language='.$record_id);
}

function prompt($dbi, $item) {
    $language = htmlspecialchars($item->ID_language, ENT_HTML5).': '.
        htmlspecialchars($item->english, ENT_HTML5).' ('.
        htmlspecialchars($item->deutsch, ENT_HTML5).')';
    return 'Remove language'.': "<em>'.$language.'</em>".';
}

remove_record('ID_language', 'ID_language', 'nmv__language', 'language', 'nmv_list_language', 'nmv_view_language?ID_language=', 'prompt', 'breadcrumb');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
