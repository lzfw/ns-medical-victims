<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('edit');

$form = new Form ('nmv_victim_literature');

// query: get victim data
$victim_id = (int) getUrlParameter('ID_victim', 0);
$victim_name = 'Error: Unknown.';
if ($victim_id) {
    $querystring = "
    SELECT CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_names, '')) victim_name
    FROM nmv__victim
    WHERE ID_victim = $victim_id";
    $query = $dbi->connection->query($querystring);
    $victim = $query->fetch_object();
    $victim_name = $victim->victim_name;
} else {
    $vict_literature_id = (int) getUrlParameter('ID_vict_lit', 0);
    $querystring = "
    SELECT CONCAT(COALESCE(v.surname, ''), ' ', COALESCE(v.first_names, '')) victim_name,
        v.ID_victim victim_id
    FROM nmv__victim v
    RIGHT JOIN nmv__victim_literature h ON (h.ID_victim = v.ID_victim)
    WHERE ID_vict_lit = $vict_literature_id";
    $query = $dbi->connection->query($querystring);
    $victim = $query->fetch_object();
    $victim_id = $victim->victim_id;
    $victim_name = $victim->victim_name;
}


$form
	->setLabel('Literature mentioning victim ' . $victim_name);

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_vict_lit');

$form->addField ('ID_victim',PROTECTED_TEXT)
    ->setLabel ('victim ID');
$form->addField ('ID_literature',SELECT,REQUIRED)
    ->setLabel ('literature')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromTable ( 'nmv__literature', 'ID_literature', "LEFT(concat(IFNULL(LEFT(lit_title, 60), '#'),' - ',IFNULL(LEFT(authors,40), '#'),' - ',IFNULL(lit_year, '#')),100)");
$form->addField ('pages',TEXT,250)
    ->setLabel ('pages');
$form->addField('literature_has_photo', CHECKBOX, -1)
        ->setLabel('literature contains photo');    

$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__victim_literature')
	->addAction (REDIRECT,'nmv_list_victim_literature?ID_victim='.$victim_id);

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Victims','nmv_list_victims');
$dbi->addBreadcrumb ('Literature describing '.$victim_name,'nmv_list_victim_literature?ID_victim='.$victim_id);

$layout
	->set('title',getUrlParameter('ID_vict_lit') ? 'Edit Victim Literature Entry' : 'New Victim Literature Entry')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
