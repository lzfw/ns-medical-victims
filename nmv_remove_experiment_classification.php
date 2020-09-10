<?php

require_once 'zefiro/ini.php';
require_once 'zefiro/lib/opendb_mysql.php';
require_once 'zefiro/lib/url.php';
require_once 'nmv_remove_base.php';

$dbi->requireUserPermission ('admin');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Experiment Classification','nmv_list_experiment_classification');

function breadcrumb($dbi, $item, $record_id) {
    $classification_names = $item->english .
        ' (' . $item->deutsch . ')';
    $dbi->addBreadcrumb ($classification_names,'nmv_view_experiment_classification?ID_exp_classification='.$record_id);
}

function prompt($dbi, $item) {
    $classification = htmlspecialchars($item->ID_exp_classification, ENT_HTML5).': '.
        htmlspecialchars($item->english, ENT_HTML5).' ('.
        htmlspecialchars($item->deutsch, ENT_HTML5).')';
    return 'Remove experiment classification'.': "<em>'.$classification.'</em>".';
}

remove_record('ID_exp_classification', 'ID_exp_classification', 'nmv__experiment_classification', 'experiment classification', 'nmv_list_experiment_classification', 'nmv_view_experiment_classification?ID_exp_classification=', 'prompt', 'breadcrumb');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb (L_USER_ACCOUNTS,'z_list_users');
