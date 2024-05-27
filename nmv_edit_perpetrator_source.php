<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('edit');

$form = new Form ('nmv_perpetrator_source');

// query: get perpetrator data
$perpetrator_id = (int) getUrlParameter('ID_perpetrator', 0);
$source_id = (int) getUrlParameter('ID_source', 0);
$perp_source_id = (int) getUrlParameter('ID_perp_source', 0);

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_perp_source');
if ($perp_source_id) {
    $form->addField ('ID_perpetrator',PROTECTED_TEXT)
        ->setLabel ('Perpetrator ID');
    $form->addField ('ID_source',SELECT, REQUIRED)
        ->setLabel ('Source')
        ->addOption (NO_VALUE,'please choose')
        ->addOptionsFromTable ( 'nmv__source', 'ID_source', "LEFT(concat(IFNULL(LEFT(source_title, 60), '#'),' - ',
                IFNULL(creation_year, '#')),100)");
    $form->addField ('ID_perpetrator',SELECT, REQUIRED)
        ->setLabel ('Perpetrator')
        ->addOption (NO_VALUE,'please choose')
        ->addOptionsFromTable ( 'nmv__perpetrator', 'ID_perpetrator', "CONCAT(IFNULL(surname, ''), ', ', IFNULL(first_names, ''))");
}
elseif ($perpetrator_id) {
//    $form
//    ->setLabel('Source linked to Perpetrator: ' . $perpetrator_name);
    $form->addField ('ID_perpetrator',PROTECTED_TEXT)
        ->setLabel ('Perpetrator ID');
    $form->addField ('ID_source',SELECT, REQUIRED)
        ->setLabel ('Source')
        ->addOption (NO_VALUE,'please choose')
        ->addOptionsFromTable ( 'nmv__source', 'ID_source', "LEFT(concat(IFNULL(LEFT(source_title, 60), '#'),' - ',
                IFNULL(creation_year, '#')),100)");
} elseif ($source_id) {
//    $form
//        ->setLabel('Perpetrator linked to Source: ' . $source_title);
    $form->addField ('ID_source',PROTECTED_TEXT)
        ->setLabel ('Source ID');
    $form->addField ('ID_perpetrator',SELECT, REQUIRED)
        ->setLabel ('Perpetrator')
        ->addOption (NO_VALUE,'please choose')
        ->addOptionsFromTable ( 'nmv__perpetrator', 'ID_perpetrator', "CONCAT(IFNULL(surname, ''), ', ', IFNULL(first_names, ''))");

}
$form->addField ('location',TEXTAREA)
    ->setClass ('keyboardInput')
    ->setLabel ('Location');
$form->addField ('url',TEXTAREA)
    ->setClass ('keyboardInput')
    ->setLabel ('URL');
$form->addField ('access_day',TEXT,2)
		->addCondition(VALUE, MIN, 1)
		->addCondition(VALUE, MAX, 31)
    ->setLabel ('Access date DDMMYYYY');
$form->addField ('access_month',TEXT,2)
		->addCondition(VALUE, MIN, 1)
		->addCondition(VALUE, MAX, 12)
		->appendTo('access_day');
$form->addField ('access_year',TEXT,4)
		->appendTo('access_day');


$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__perpetrator_source');
if ($perpetrator_id) {
    $form
        ->addAction(REDIRECT, 'nmv_list_perpetrator_source?ID_perpetrator=' . $perpetrator_id);
} elseif ($source_id) {
    $form
        ->addAction(REDIRECT, 'nmv_list_perpetrator_source?ID_source=' . $source_id);
}
$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Perpetrators','nmv_list_perpetrators');
//$dbi->addBreadcrumb ('Sources mentioning '.$perpetrator_name,'nmv_list_perpetrator_source?ID_perpetrator='.$perpetrator_id);

$layout
	->set('title',getUrlParameter('ID_perp_source') ? 'Edit Perpetrator Source Entry' : 'New Perpetrator Source Entry')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
