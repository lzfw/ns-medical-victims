<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('edit');
$form = new Form('nmv_edit_experiment');

$form
	->setLabel('Biomedical Research');

$form
	->addConnection(MYSQL_DB, $db_host, $db_user, $db_pass, $db_name)
	->setPrimaryKeyName('ID_experiment');
$form->addField('experiment_title', TEXT, 255, REQUIRED)
    ->setClass('keyboardInput')
    ->setLabel('Title');
$form->addField('ID_exp_classification' ,SELECT)
    ->setLabel('Classification')
    ->addOption(NO_VALUE, 'please choose')
    ->addOptionsFromTable( 'nmv__experiment_classification', 'ID_exp_classification', 'classification');
$form->addField('location_details', TEXT, 250)
    ->setClass('keyboardInput')
    ->setLabel('Location Details');
$form->addField('start_day', TEXT, 2)
    ->setLabel('Start DMYYYY')
    ->addCondition(VALUE, MIN, 0)
    ->addCondition(VALUE, MAX, 31);
$form->addField('start_month', TEXT, 2)
    ->addCondition(VALUE, MIN, 0)
    ->addCondition(VALUE, MAX, 12)
    ->appendTo('start_day');
$form->addField('start_year', TEXT, 4)
    ->addCondition(VALUE, MIN, 0)
    ->addCondition(VALUE, MAX, 1950)
    ->appendTo('start_day');
$form->addField('end_day', TEXT,2)
    ->setLabel('End DMYYYY')
    ->addCondition(VALUE, MIN, 0)
    ->addCondition(VALUE, MAX, 31);
$form->addField('end_month', TEXT, 2)
    ->addCondition(VALUE, MIN, 0)
    ->addCondition(VALUE, MAX, 12)
    ->appendTo('end_day');
$form->addField('end_year', TEXT, 4)
    ->addCondition(VALUE, MIN, 0)
    ->addCondition(VALUE, MAX, 1999)
    ->appendTo('end_day');
$form->addField('notes_location', TEXTAREA)
    ->setClass('keyboardInput')
    ->setLabel('Notes (Location)');
$form->addField('funding', TEXT, 250)
    ->setClass('keyboardInput')
    ->setLabel('Funding');
$form->addField('objective', TEXT, 250)
    ->setLabel('Objective');
$form->addField('number_victims_estimate', TEXT ,6)
    ->setLabel('Estimated Number of Victims')
    ->addCondition(VALUE, MIN, -1)
    ->addCondition(VALUE, MAX, 1000000);
$form->addField('number_fatalities_estimate', TEXT ,6)
    ->setLabel('Estimated Number of Fatalities')
    ->addCondition(VALUE, MIN, -1)
    ->addCondition(VALUE, MAX, 1000000);
$form->addField('number_victims_remark', TEXTAREA)
    ->setLabel('Remark about Number of Victims');
$form->addField('confirmed_experiment', CHECKBOX, 1)
    ->setLabel('Confirmed Biomedical Research');
$form->addField('notes', TEXTAREA)
    ->setClass('keyboardInput')
    ->setLabel('Notes');
$form->addField('visibility', RADIO, '', '')
    ->setLabel('Visibility')
    ->addRadioButton('public', ' public (visible for every website-visitor)')
    ->addRadioButton('restricted', ' restricted (visible on the website after login)')
    ->addRadioButton('hidden', ' hidden (not visible on website)');
$form->addField('foi', STATIC_TEXT, '<hr>	<strong>Fields of Interest and Institutions</strong> can be edited in the Experiment View.<br>
				 									Click OK-Button in order to save your changes and switch to View.<hr>');

$form
	->addButton(SUBMIT)
	->addButton(APPLY);

$form
	->addAction(DATABASE, 'nmv__experiment')
	->addAction(REDIRECT, 'nmv_view_experiment?ID_experiment={ID_experiment}');


$dbi->addBreadcrumb(L_CONTENTS, 'z_menu_contents');
$dbi->addBreadcrumb('Biomedical Research', 'nmv_list_experiments');

$layout
	->set('title',getUrlParameter('ID_experiment') ? 'Edit Biomedical Research' : 'New Biomedical Research')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
