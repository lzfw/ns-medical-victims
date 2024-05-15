<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('edit');

$form = new Form ('nmv_victim_source');

// query: get victim data
$victim_id = (int) getUrlParameter('ID_victim', 0);
$source_id = (int) getUrlParameter('ID_source', 0);
$vict_source_id = (int) getUrlParameter('ID_vict_source', 0);

/** Requires check of User permission - User needs to have permission to edit.
 * If profile is from TEilprojekte Berlin/Vienna/Munich special permission 'all' is needed
 *
 * @var DBI $dbi Calls method require user permission.
 * @see
 */
//OBACHT
$ID_victim = (int) getUrlParameter('ID_victim', 0);
if($ID_victim >= 46028 && $ID_victim <= 46126 || $ID_victim >= 46259 && $ID_victim <= 47647):
    $dbi->requireUserPermission('all');
else:
    $dbi->requireUserPermission ('edit');
endif;

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_vict_source');
if ($vict_source_id) {
    $form->addField('ID_vict_source', PROTECTED_TEXT)
        ->setLabel('Victim-Source ID');
    $form->addField('ID_source', SELECT, REQUIRED)
        ->setLabel('Source')
        ->addOption(NO_VALUE, 'please choose')
        ->addOptionsFromTable('nmv__source', 'ID_source', "LEFT(concat(IFNULL(LEFT(source_title, 60), '#'),' - ', 
                IFNULL(creation_year, '#')),100)");
    $form->addField('ID_victim', TEXT, 10)
        ->setLabel('Person ID in this database');
}
elseif ($victim_id) {
//    $form
//        ->setLabel('Source linked to Person: ' . $victim_name);
    $form->addField('ID_victim', PROTECTED_TEXT)
        ->setLabel('Victim ID');
    $form->addField('ID_source', SELECT, REQUIRED)
        ->setLabel('Source')
        ->addOption(NO_VALUE, 'please choose')
        ->addOptionsFromTable('nmv__source', 'ID_source', "LEFT(concat(IFNULL(LEFT(source_title, 60), '#'),
                        ' - ',IFNULL(creation_year, '#')),100)");
} elseif ($source_id) {
//    $form
//        ->setLabel('Person linked to Source: ' . $source_title);
    $form->addField('ID_source', PROTECTED_TEXT)
        ->setLabel('Source ID');
    $form->addField('ID_victim', TEXT, 10)
        ->setLabel('Person ID in this database');
}
$form->addField ('location', TEXTAREA)
    ->setClass ('keyboardInput')
    ->setLabel ('location');
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
$form->addField('source_has_photo', CHECKBOX, -1)
    ->setLabel('source contains photo');

$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__victim_source');
if ($victim_id) {
    $form
        ->addAction(REDIRECT, 'nmv_list_victim_literature_and_sources?ID_victim=' . $victim_id);
} elseif ($source_id) {
    $form
        ->addAction(REDIRECT, 'nmv_list_victim_source?ID_source=' . $source_id);
}

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Victims','nmv_list_victims');
//$dbi->addBreadcrumb ('Sources mentioning '.$victim_name,'nmv_list_victim_source?ID_victim='.$victim_id);

$layout
	->set('title',getUrlParameter('ID_vict_source') ? 'Edit Person Source Link' : 'New Person Source Link')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
