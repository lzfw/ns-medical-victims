<?php

require_once 'zefiro/ini.php';
require_once 'zefiro/lib/opendb_mysql.php';
require_once 'zefiro/lib/url.php';
require_once 'nmv_remove_base.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Sources','nmv_list_sources');

function breadcrumb($dbi, $item, $record_id) {
    $source_name = $item->source_title;
    $dbi->addBreadcrumb ($source_name,'nmv_view_source?ID_source='.$record_id);
}

function prompt($dbi, $item) {
    $source = htmlspecialchars($item->ID_source, ENT_HTML5).': '.
        htmlspecialchars($item->source_title, ENT_HTML5).': <small>'.
        htmlspecialchars($item->description, ENT_HTML5) . '</small>';
    return 'Remove Source'.': "<em>'.$source.'</em>".';
}

remove_record('ID_source', 'ID_source', 'nmv__source', 'source', 'nmv_list_sources', 'nmv_view_source?ID_source=', 'prompt', 'breadcrumb');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
