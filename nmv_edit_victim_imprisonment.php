<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('edit');

$form = new Form ('nmv_edit_victim_imprisonment');
$tag_array = array();
$tag_button = '';
// query: get victim data
$victim_id = (int) getUrlParameter('ID_victim', 0);
$imprisonment_id = (int) getUrlParameter('ID_imprisonment', 0);

$victim_name = 'Error: Unknown.';
if ($victim_id) {
    $querystring = "
    SELECT CONCAT(surname, ' ', first_names) victim_name
    FROM nmv__victim
    WHERE ID_victim = $victim_id";
    $query = $dbi->connection->query($querystring);
    $victim = $query->fetch_object();
    $victim_name = $victim->victim_name;
} else {
    $name_id = (int) getUrlParameter('ID_imprisonment', 0);
    $querystring = "
    SELECT CONCAT(v.surname, ' ', v.first_names) victim_name,
        v.ID_victim victim_id
    FROM nmv__victim v
    RIGHT JOIN nmv__imprisonment i ON (i.ID_victim = v.ID_victim)
    WHERE ID_imprisonment = $name_id";
    $query = $dbi->connection->query($querystring);
    $victim = $query->fetch_object();
    $victim_id = $victim->victim_id;
    $victim_name = $victim->victim_name;
}
// query: get array of classification for this imprisonment
$tagged = $dbi->connection->query("SELECT ic.ID_classification, vc.english
                                   FROM nmv__imprisonment_classification ic
                                   LEFT JOIN nmv__victim_classification vc ON vc.ID_classification = ic.ID_classification
                                   WHERE ic.ID_imprisonment = $imprisonment_id");
while ($tag = $tagged->fetch_row()) {
	$tag_array[] = $tag[1];
}

$form
	->setLabel('Imprisonment: ' . $victim_name);
$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_imprisonment');
$form->addField ('ID_victim',PROTECTED_TEXT)
    ->setLabel ('ID person');
$form->addField ('number',TEXT,50)
    ->setLabel ('(Prison) Number');
$form->addField('ID_institution', SELECT)
    ->setLabel('Institution')
    ->addOption (NO_VALUE, 'please choose')
    ->addOptionsFromTable('nmv__institution', 'ID_institution', 'institution_name', 'type NOT IN (23,24, 18, 19, 1)');
$form->addField ('location',TEXT,255)
    ->setClass ('keyboardInput')
    ->setLabel ('Location');
$form->addField ('start_day',TEXT,2)
    ->setLabel ('Start imprisonment DMYYYY')
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,31);
$form->addField ('start_month',TEXT,2)
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,12)
    ->appendTo('start_day');
$form->addField ('start_year',TEXT,4)
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,1950)
    ->appendTo('start_day');
$form->addField('classification', STATIC_TEXT, "<strong>Classification(s):</strong><br><ul class='inside'><li>" . implode('</li><li>', $tag_array) . '</li></ul>');
$form->addField('classification_info', STATIC_TEXT, "Classifications can be edited in the Imprisonment table in the Victim View.
Click OK-Button in order to save your changes and switch to View.");


$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__imprisonment')
	->addAction (REDIRECT,'nmv_view_victim?ID_victim='.$victim_id);

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Victims','nmv_list_victims');
$dbi->addBreadcrumb ($victim_name,'nmv_view_victim?ID_victim='.$victim_id);

$layout
	->set('title',getUrlParameter('ID_imprisonment') ? 'Edit Imprisonment' : 'New Imprisonment')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
