<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('edit');

$form = new Form ('nmv_victim_source');

// query: get victim data
$victim_id = (int) getUrlParameter('ID_victim', 0);
$source_id = (int) getUrlParameter('ID_source', 0);
$source_title = 'Error: Unknown';
$victim_name = 'Error: Unknown.';
if($victim_id){
    $querystring = "
    SELECT CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_names, '')) victim_name
    FROM nmv__victim
    WHERE ID_victim = $victim_id";
    $query = $dbi->connection->query($querystring);
    $victim = $query->fetch_object();
    $victim_name = $victim->victim_name;
}elseif($source_id){
    $querystring = "
    SELECT source_title
    FROM nmv__source
    WHERE ID_source = $source_id";
    $query = $dbi->connection->query($querystring);
    $source = $query->fetch_object();
    $source_title = $source->source_title;
}

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_vict_source');
if($victim_id){
    $form
        ->setLabel('Source mentioning Person: ' . $victim_name);
    $form->addField('ID_victim', PROTECTED_TEXT)
        ->setLabel('Victim ID');
    $form->addField('ID_source', SELECT, REQUIRED)
        ->setLabel('Source')
        ->addOption(NO_VALUE, 'please choose')
        ->addOptionsFromTable('nmv__source', 'ID_source', "LEFT(concat(IFNULL(LEFT(source_title, 60), '#'),' - ',IFNULL(LEFT(medium,40), '#'),' - ',IFNULL(creation_year, '#')),100)");
}elseif($source_id){
    $form
        ->setLabel('Person mentioned in Source: ' . $source_title);
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
	->addAction (DATABASE,'nmv__victim_source')
	->addAction (REDIRECT,'nmv_list_victim_literature_and_sources?ID_victim='.$victim_id);

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Victims','nmv_list_victims');
$dbi->addBreadcrumb ('Sources mentioning '.$victim_name,'nmv_list_victim_source?ID_victim='.$victim_id);

$layout
	->set('title',getUrlParameter('ID_vict_source') ? 'Edit Person Source Link' : 'New Person Source Link')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
