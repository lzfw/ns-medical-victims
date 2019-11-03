<?php

require_once 'zefiro/ini.php';
require_once 'zefiro/lib/opendb_mysql.php';
require_once 'zefiro/lib/url.php';
require_once 'nmv_remove_base.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Biomedical Research','nmv_list_experiments');

function breadcrumb($dbi, $item, $record_id) {
    $experiment_name = $item->experiment_title;
    $dbi->addBreadcrumb ($experiment_name,'nmv_view_experiment?ID_experiment='.$record_id);
}

function prompt($dbi, $item) {
    $experiment = htmlspecialchars($item->ID_experiment, ENT_HTML5).': '.
        htmlspecialchars($item->experiment_title, ENT_HTML5).' '.
        htmlspecialchars($item->field_of_interest, ENT_HTML5);
    return 'Remove Biomedical Research: "<em>'.$experiment.'</em>".';
}

remove_record('ID_experiment', 'ID_experiment', 'nmv__experiment', 'Biomedical Research', 'nmv_list_experiments', 'nmv_view_experiment?ID_experiment=', 'prompt', 'breadcrumb');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
