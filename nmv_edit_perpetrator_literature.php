<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('edit');

$form = new Form ('nmv_perpetrator_literature');

// query: get perpetrator data
$perpetrator_id = (int) getUrlParameter('ID_perpetrator', 0);
$perpetrator_name = 'Error: Unknown.';
if ($perpetrator_id) {
    $querystring = "
    SELECT CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_names, '')) perpetrator_name
    FROM nmv__perpetrator
    WHERE ID_perpetrator = $perpetrator_id";
    $query = $dbi->connection->query($querystring);
    $perpetrator = $query->fetch_object();
    $perpetrator_name = $perpetrator->perpetrator_name;
} else {
    $vict_literature_id = (int) getUrlParameter('ID_perp_lit', 0);
    $querystring = "
    SELECT CONCAT(COALESCE(v.surname, ''), ' ', COALESCE(v.first_names, '')) perpetrator_name,
        v.ID_perpetrator perpetrator_id
    FROM nmv__perpetrator v
    RIGHT JOIN nmv__perpetrator_literature h ON (h.ID_perpetrator = v.ID_perpetrator)
    WHERE ID_perp_lit = $vict_literature_id";
    $query = $dbi->connection->query($querystring);
    $perpetrator = $query->fetch_object();
    $perpetrator_id = $perpetrator->perpetrator_id;
    $perpetrator_name = $perpetrator->perpetrator_name;
}


$form
	->setLabel('Literature mentioning perpetrator ' . $perpetrator_name);

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_perp_lit');

$form->addField ('ID_perpetrator',PROTECTED_TEXT)
    ->setLabel ('perpetrator ID');
$form->addField ('ID_literature',SELECT,REQUIRED)
    ->setLabel ('literature')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromTable ( 'nmv__literature', 'ID_literature', "LEFT(concat(IFNULL(LEFT(lit_title, 60), '#'),' - ',IFNULL(LEFT(authors,40), '#'),' - ',IFNULL(lit_year, '#')),100)");
$form->addField ('pages',TEXT,250)
    ->setLabel ('pages');

$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__perpetrator_literature')
	->addAction (REDIRECT,'nmv_list_perpetrator_literature?ID_perpetrator='.$perpetrator_id);

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Perpetrators','nmv_list_perpetrators');
$dbi->addBreadcrumb ('Literature describing '.$perpetrator_name,'nmv_list_perpetrator_literature?ID_perpetrator='.$perpetrator_id);

$layout
	->set('title',getUrlParameter('ID_perp_lit') ? 'Edit Perpetrator Literature Entry' : 'New Perpetrator Literature Entry')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
