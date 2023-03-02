<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('edit');

$form = new Form ('nmv_victim_literature');

// query: get victim data
$victim_id = (int) getUrlParameter('ID_victim', 0);
$literature_id = (int) getUrlParameter('ID_literature', 0);
$vict_literature_id = (int) getUrlParameter('ID_vict_lit', 0);

//$lit_title = 'Error: Unknown.';
//$victim_name = 'Error: Unknown.';
//if($victim_id){
//    $querystring = "
//    SELECT CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_names, '')) victim_name
//    FROM nmv__victim
//    WHERE ID_victim = $victim_id";
//    $query = $dbi->connection->query($querystring);
//    $victim = $query->fetch_object();
//    $victim_name = $victim->victim_name;
//} elseif($literature_id) {
//    $querystring = "
//    SELECT lit_title
//    FROM nmv__literature
//    WHERE ID_literature = $literature_id";
//    $query = $dbi->connection->query($querystring);
//    $literature = $query->fetch_object();
//    $lit_title = $literature->lit_title;
//} else {
//    $querystring = "
//    SELECT CONCAT(COALESCE(v.surname, ''), ' ', COALESCE(v.first_names, '')) victim_name,
//        v.ID_victim victim_id
//    FROM nmv__victim v
//    RIGHT JOIN nmv__victim_literature h ON (h.ID_victim = v.ID_victim)
//    WHERE ID_vict_lit = $vict_literature_id";
//    $query = $dbi->connection->query($querystring);
//    $victim = $query->fetch_object();
//    $victim_id = $victim->victim_id;
//    $victim_name = $victim->victim_name;
//}

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
