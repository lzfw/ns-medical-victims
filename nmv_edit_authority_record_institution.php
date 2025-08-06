<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

/** Requires check of User permission - User needs to have permission to edit.
 *
 * @var DBI $dbi Calls method require user permission.
 * @see
 */

$authority_record_id = (int) getUrlParameter('ID_authority_record_institution', 0);

$form = new Form ('nmv_edit_authority_record_institution');

// query: get institution data
$institution_id = (int) getUrlParameter('ID_institution', 0);
$institution_name = 'Error: Unknown.';
if ($institution_id) {
    $querystring = "
    SELECT i.institution_name, i.ID_institution
    FROM nmv__institution i
    WHERE i.ID_institution = $institution_id";
    $query = $dbi->connection->query($querystring);
    $institution = $query->fetch_object();
    $institution_name = $institution->institution_name;
} else {
    $querystring = "
    SELECT i.institution_name, i.ID_institution        
    FROM nmv__institution i
    RIGHT JOIN nmv__authority_record_institution ar ON (ar.ID_institution = i.ID_institution)
    WHERE ID_authority_record_institution = $authority_record_id";
    $query = $dbi->connection->query($querystring);
    $institution = $query->fetch_object();
    $institution_id = $institution->ID_institution;
    $institution_name = $institution->institution_name;
}


$form
	->setLabel('Authority record for ' . $institution_name);

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_authority_record_institution');

$form->addField('ID_institution',PROTECTED_TEXT)
    ->setLabel('ID institution');
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
	->addAction (DATABASE,'nmv__authority_record_institution')
	->addAction (REDIRECT,'nmv_view_institution?ID_institution='.$institution_id);

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Institutions','nmv_list_institutions');
$dbi->addBreadcrumb ($institution_name,'nmv_view_institution?ID_institution='.$institution_id);

$layout
	->set('title',getUrlParameter('ID_authority_record_institution') ? 'Edit Authority Record' : 'New Authority Record')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
