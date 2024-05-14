<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('edit');

$form = new Form ('nmv_perpetrator_literature');

// query: get perpetrator data
$perpetrator_id = (int) getUrlParameter('ID_perpetrator', 0);
$literature_id = (int) getUrlParameter('ID_literature', 0);
$perp_literature_id = (int) getUrlParameter('ID_perp_lit', 0);


$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_perp_lit');
if ($perp_literature_id) {
    $form->addField('ID_perpetrator', PROTECTED_TEXT)
        ->setLabel('Perpetrator ID');
    $form->addField('ID_literature', SELECT, REQUIRED)
        ->setLabel('Literature')
        ->addOption(NO_VALUE, 'please choose')
        ->addOptionsFromTable('nmv__literature', 'ID_literature', "LEFT(concat(IFNULL(LEFT(lit_title, 60), '#'),' - ',
        IFNULL(LEFT(authors,40), '#'),' - ',IFNULL(lit_year, '#')),100)");
    $form->addField('ID_perpetrator', SELECT, REQUIRED)
        ->setLabel('Perpetrator')
        ->addOption(NO_VALUE, 'please choose')
        ->addOptionsFromTable('nmv__perpetrator', 'ID_perpetrator', "CONCAT(IFNULL(surname, ''), ', ', IFNULL(first_names, ''))");
}
elseif ($perpetrator_id) {
//    $form
//        ->setLabel('Literature linked to perpetrator: ' . $perpetrator_name);
    $form->addField('ID_perpetrator', PROTECTED_TEXT)
        ->setLabel('Perpetrator ID');
    $form->addField('ID_literature', SELECT, REQUIRED)
        ->setLabel('Literature')
        ->addOption(NO_VALUE, 'please choose')
        ->addOptionsFromTable('nmv__literature', 'ID_literature', "LEFT(concat(IFNULL(LEFT(lit_title, 60), '#'),' - ',
        IFNULL(LEFT(authors,40), '#'),' - ',IFNULL(lit_year, '#')),100)");
} elseif ($literature_id) {
//    $form
//        ->setLabel('Perpetrator linked to literature: ' . $lit_title);
    $form->addField('ID_literature', PROTECTED_TEXT)
        ->setLabel('Literature ID');
    $form->addField('ID_perpetrator', SELECT, REQUIRED)
        ->setLabel('Perpetrator')
        ->addOption(NO_VALUE, 'please choose')
        ->addOptionsFromTable('nmv__perpetrator', 'ID_perpetrator', "CONCAT(IFNULL(surname, ''), ', ', IFNULL(first_names, ''))");
}
$form->addField ('pages',TEXT,250)
    ->setLabel ('pages');
$form->addField('literature_has_photo', CHECKBOX, -1)
    ->setLabel('literature contains photo');

$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__perpetrator_literature');
if ($perpetrator_id) {
    $form
        ->addAction(REDIRECT, 'nmv_list_perpetrator_literature?ID_perpetrator=' . $perpetrator_id);
} elseif ($literature_id) {
    $form
        ->addAction(REDIRECT, 'nmv_list_perpetrator_literature?ID_literature=' . $literature_id);
}

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Perpetrators','nmv_list_perpetrators');
//$dbi->addBreadcrumb ('Literature describing '.$perpetrator_name,'nmv_list_perpetrator_literature?ID_perpetrator='.$perpetrator_id);

$layout
	->set('title',getUrlParameter('ID_perp_lit') ? 'Edit Perpetrator Literature Link' : 'New Perpetrator Literature Link')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
