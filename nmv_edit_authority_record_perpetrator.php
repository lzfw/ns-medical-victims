<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

/** Requires check of User permission - User needs to have permission to edit.
 *
 * @var DBI $dbi Calls method require user permission.
 * @see
 */

$authority_record_id = (int) getUrlParameter('ID_authority_record_perpetrator', 0);

$form = new Form ('nmv_edit_authority_record_perpetrator');

// query: get institution data
$perpetrator_id = (int) getUrlParameter('ID_perpetrator', 0);
$perpetrator_name = 'Error: Unknown.';
if ($perpetrator_id) {
    $querystring = "
    SELECT CONCAT(surname, ' ', first_names) AS perpetrator_name, p.ID_perpetrator
    FROM nmv__perpetrator p
    WHERE p.ID_perpetrator = $perpetrator_id";
    $query = $dbi->connection->query($querystring);
    $perpetrator = $query->fetch_object();
    $perpetrator_name = $perpetrator->perpetrator_name;
} else {
    $querystring = "
    SELECT CONCAT(surname, ' ', first_names) AS perpetrator_name, p.ID_perpetrator        
    FROM nmv__perpetrator p
    RIGHT JOIN nmv__authority_record_perpetrator ar ON (ar.ID_perpetrator = p.ID_perpetrator)
    WHERE ID_authority_record_perpetrator = $authority_record_id";
    $query = $dbi->connection->query($querystring);
    $perpetrator = $query->fetch_object();
    $perpetrator_id = $perpetrator->ID_perpetrator;
    $perpetrator_name = $perpetrator->perpetrator_name;
}


$form
	->setLabel('Authority record for ' . $perpetrator_name);

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_authority_record_perpetrator');

$form->addField('ID_perpetrator',PROTECTED_TEXT)
    ->setLabel('ID perpetrator');
$form->addField('separator_0', STATIC_TEXT, '<hr>');
$form->addField('authority_type', RADIO, '', '')
    ->setLabel('authority type')
    ->addRadioButton('GND', ' GND')
    ->addRadioButton('VIAF', ' VIAF')
    ->addRadioButton('wikidata', ' wikidata');
$form->addField ('authority_id',TEXT,50,REQUIRED)
    ->setClass ('keyboardInput')
    ->setLabel ('authority id');

$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__authority_record_perpetrator')
	->addAction (REDIRECT,'nmv_view_perpetrator?ID_perpetrator='.$perpetrator_id);

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Perpetrators','nmv_list_perpetrators');
$dbi->addBreadcrumb ($perpetrator_name,'nmv_view_perpetrator?ID_perpetrator='.$perpetrator_id);

$layout
	->set('title',getUrlParameter('ID_authority_record_institution') ? 'Edit Authority Record' : 'New Authority Record')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
