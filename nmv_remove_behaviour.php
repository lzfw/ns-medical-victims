<?php

require_once 'zefiro/ini.php';
require_once 'zefiro/lib/opendb_mysql.php';
require_once 'zefiro/lib/url.php';
require_once 'nmv_remove_base.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Behaviour','nmv_list_behaviour');

function breadcrumb($dbi, $item, $record_id) {
    $behaviour_names = $item->english .
        ' (' . $item->deutsch . ')';
    $dbi->addBreadcrumb ($behaviour_names,'nmv_view_behaviour?ID_behaviour='.$record_id);
}

function prompt($dbi, $item) {
    $behaviour = htmlspecialchars($item->ID_behaviour, ENT_HTML5).': '.
        htmlspecialchars($item->english, ENT_HTML5).' ('.
        htmlspecialchars($item->deutsch, ENT_HTML5).')';
    return 'Remove behaviour'.': "<em>'.$behaviour.'</em>".';
}

remove_record('ID_behaviour', 'ID_behaviour', 'nmv__behaviour', 'behaviour', 'nmv_list_behaviour', 'nmv_view_behaviour?ID_behaviour=', 'prompt', 'breadcrumb');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
