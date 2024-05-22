<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('edit');

$form = new Form ('nmv_victim_literature');

// query: get victim data
$victim_id = (int) getUrlParameter('ID_victim', 0);
$literature_id = (int) getUrlParameter('ID_literature', 0);
$vict_literature_id = (int) getUrlParameter('ID_vict_lit', 0);

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
	->setPrimaryKeyName('ID_vict_lit');
if ($vict_literature_id){
    $form->addField('ID_vict_lit', PROTECTED_TEXT)
        ->setLabel('Victim-Literature ID');
    $form->addField('ID_literature', SELECT, REQUIRED)
        ->setLabel('Literature')
        ->addOption(NO_VALUE, 'please choose')
        ->addOptionsFromTable('nmv__literature', 'ID_literature', "LEFT(concat(IFNULL(LEFT(lit_title, 60), '#'),' - ',IFNULL(LEFT(authors,40), '#'),' - ',IFNULL(lit_year, '#')),100)");
    $form->addField('ID_victim', TEXT, 10)
        ->setLabel('Victim ID in this database');
}
elseif($victim_id) {
//    $form
//        ->setLabel('Literature linked to Person: ' . $victim_name);
    $form->addField('ID_victim', PROTECTED_TEXT)
        ->setLabel('Victim ID');
    $form->addField('ID_literature', SELECT, REQUIRED)
        ->setLabel('Literature')
        ->addOption(NO_VALUE, 'please choose')
        ->addOptionsFromTable('nmv__literature', 'ID_literature', "LEFT(concat(IFNULL(LEFT(lit_title, 60), '#'),' - ',IFNULL(LEFT(authors,40), '#'),' - ',IFNULL(lit_year, '#')),100)");
}elseif($literature_id){
//    $form
//        ->setLabel('Person linked to Literature: ' . $lit_title);
    $form->addField('ID_literature', PROTECTED_TEXT)
        ->setLabel('LiteratureID');
    $form->addField('ID_victim', TEXT, 10)
        ->setLabel('Victim ID in this database');
}

$form->addField ('pages',TEXT,250)
    ->setLabel ('pages');
$form->addField('literature_has_photo', CHECKBOX, -1)
        ->setLabel('literature contains photo');

$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__victim_literature');
if ($victim_id) {
    $form
        ->addAction(REDIRECT, 'nmv_list_victim_literature_and_sources?ID_victim=' . $victim_id);
} elseif ($literature_id) {
    $form
        ->addAction(REDIRECT, 'nmv_list_victim_literature?ID_literature=' . $literature_id);
}
$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Victims','nmv_list_victims');
//$dbi->addBreadcrumb ('Literature describing '.$victim_name,'nmv_list_victim_literature?ID_victim='.$victim_id);

$layout
	->set('title',getUrlParameter('ID_vict_lit') ? 'Edit Person Literature Link' : 'New Person Literature Link')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
