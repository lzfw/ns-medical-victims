<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('edit');

$form = new Form ('nmv_edit_perpetrator_qualification');
$tag_array = array();
$tag_button = '';
// query: get perpetrator data
$perpetrator_id = (int) getUrlParameter('ID_perpetrator', 0);
$qualification_id = (int) getUrlParameter('ID_qualification', 0);

$perpetrator_name = 'Error: Unknown.';
if ($perpetrator_id) {
    $querystring = "
    SELECT CONCAT(surname, ' ', first_names) perpetrator_name
    FROM nmv__perpetrator
    WHERE ID_perpetrator = $perpetrator_id";
    $query = $dbi->connection->query($querystring);
    $perpetrator = $query->fetch_object();
    $perpetrator_name = $perpetrator->perpetrator_name;
} else {
    $qualification_id = (int) getUrlParameter('ID_qualification', 0);
    $querystring = "
    SELECT CONCAT(p.surname, ' ', p.first_names) perpetrator_name,
        p.ID_perpetrator perpetrator_id
    FROM nmv__perpetrator p
    RIGHT JOIN nmv__qualification q ON (q.ID_perpetrator = p.ID_perpetrator)
    WHERE ID_qualification = $qualification_id";
    $query = $dbi->connection->query($querystring);
    $perpetrator = $query->fetch_object();
    $perpetrator_id = $perpetrator->perpetrator_id;
    $perpetrator_name = $perpetrator->perpetrator_name;
}

$form
	->setLabel('Qualification: ' . $perpetrator_name);
$form
	->addConnection(MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_qualification');
$form->addField('ID_perpetrator',PROTECTED_TEXT)
    ->setLabel('ID perpetrator');
$form->addField('ID_qualification',PROTECTED_TEXT)
    ->setLabel('ID qualification');
$form->addField('qualification_year', TEXT, 4)
    ->setLabel('Year of qualification');
$form->addField('qualification_place', TEXT, 255)
    ->setLabel('Place of Qualification');
$form->addField('qualification_type', TEXT, 255)
    ->setLabel('Type of Qualification');
$form->addField('thesis_title', TEXTAREA)
    ->setLabel('Title of Thesis');   



$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__qualification')
	->addAction (REDIRECT,'nmv_view_perpetrator?ID_perpetrator='.$perpetrator_id);

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Perpetrators','nmv_list_perpetrators');
$dbi->addBreadcrumb ($perpetrator_name,'nmv_view_perpetrator?ID_perpetrator='.$perpetrator_id);

$layout
	->set('title',getUrlParameter('ID_qualification') ? 'Edit qualification' : 'New qualification')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
