<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('edit');

$form = new Form ('nmv_perpetrator_literature');

// query: get perpetrator data
$perpetrator_id = (int) getUrlParameter('ID_perpetrator', 0);
$literature_id = (int) getUrlParameter('ID_literature', 0);
$perp_literature_id = (int) getUrlParameter('ID_perp_lit', 0);


//$perpetrator_name = 'Error: Unknown.';
//$lit_title = 'Error: Unknown.';
//if ($perpetrator_id) {
//    $querystring = "
//    SELECT CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_names, '')) perpetrator_name
//    FROM nmv__perpetrator
//    WHERE ID_perpetrator = $perpetrator_id";
//    $query = $dbi->connection->query($querystring);
//    $perpetrator = $query->fetch_object();
//    $perpetrator_name = $perpetrator->perpetrator_name;
//} elseif ($literature_id) {
//    $querystring = "
//    SELECT lit_title, authors
//    FROM nmv__literature
//    WHERE ID_literature = $literature_id";
//    $query = $dbi->connection->query($querystring);
//    $literature = $query->fetch_object();
//    $lit_title = $literature->lit_title;
//} else {
//    $querystring = "
//    SELECT CONCAT(COALESCE(p.surname, ''), ' ', COALESCE(p.first_names, '')) perpetrator_name,
//        p.ID_perpetrator perpetrator_id
//    FROM nmv__perpetrator p
//    RIGHT JOIN nmv__perpetrator_literature h ON (h.ID_perpetrator = p.ID_perpetrator)
//    WHERE ID_perp_lit = $perp_literature_id";
//    $query = $dbi->connection->query($querystring);
//    $perpetrator = $query->fetch_object();
//    $perpetrator_id = $perpetrator->perpetrator_id;
//    $perpetrator_name = $perpetrator->perpetrator_name;
//}




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
