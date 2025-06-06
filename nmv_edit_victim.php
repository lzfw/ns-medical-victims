<?php

namespace EditForm;

require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

use DBI;
use Form;
use Layout;

/**
 * Creates a html form for editing or creating a victim entry.
 *
 *
 *
 * @see class /flotilla/classes/form Creates html form elements.
 * @see class /flotilla/classes/connection_mysql Opens connection to MySQL-database.
 * @see class /zefiro/lib/dbi Checks if user has permission to edit and create entries.
 * @see class /zefiro/lib/layout
 * @see file /zefiro/ini Initialises Zefiro and requires needed files to build Webapplication.
 * @var string $db_host
 * @var string $db_user
 * @var string $db_pass
 * @var string $db_name
 * @var Layout $layout
 */


/** Requires check of User permission - User needs to have permission to edit.
 * If profile is from TEilprojekte Berlin/Vienna/Munich special permission 'all' is needed
 *
 * @var DBI $dbi Calls method require user permission.
 * @see
 */
//OBACHT
$ID_victim = (int) getUrlParameter('ID_victim', 0);
if($ID_victim >= 46028 && $ID_victim <= 46126 || $ID_victim >= 46259 && $ID_victim <= 47647):
    $dbi->requireUserPermission('all');
else:
    $dbi->requireUserPermission ('edit');
endif;

$form = new Form ('nmv_edit_victim');

$form
    ->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
    ->setPrimaryKeyName('ID_victim');
$form->addField('ID_victim', PROTECTED_TEXT)
    ->setLabel('ID');
$form->addField('surname', TEXT, 255)
    ->setClass('keyboardInput')
    ->setLabel('Surname');
$form->addField('first_names', TEXT, 50)
    ->setClass('keyboardInput')
    ->setLabel('First Names');
$form->addField('visibility', RADIO, '', '')
    ->setLabel('Visibility')
    ->addRadioButton('public', ' public (visible for every website-visitor)')
    ->addRadioButton('restricted', ' restricted (visible on the website after login)')
    ->addRadioButton('hidden', ' hidden (not visible on website)');
$form->addField('display_marker', SELECT)
    ->setLabel('Markers for special display features on Website')
    ->addOption(NO_VALUE, 'no special marker (default)')
    ->addOption('research prior to 2017');
$form->addField('separator_0', STATIC_TEXT, '<hr>');
$form->addField('MPG-Project only', STATIC_TEXT, '<span class="mpgcolor">Only for MPG-project-profiles!<span>');
$form->addField('mpg_project', CHECKBOX, -1)
    ->setLabel('MPG project');
$form->addField('ID_dataset_origin', SELECT)
    ->setLabel('Origin Dataset')
    ->addOption(NO_VALUE,'please choose')
    ->addOptionsFromTableOrderedById('nmv__dataset_origin', 'ID_dataset_origin', 'work_group');
$form->addField(('ID_new_profile'), TEXT, 11)
    ->setLabel('ID of mpg profile (creates link)');
$form->addField('potential_old_profile', CHECKBOX, -1)
    ->setLabel('Potential pre-project profile');
$form->addField('s1', STATIC_TEXT, '<br>');

$form->addField('entry_status', RADIO, '', '')
    ->setLabel('Status Data Transfer')
    ->addRadioButton('Data entry (Halle)', ' Data entry (Halle)')
    ->addRadioButton('Review (Vienna/Berlin/Munich)', ' Review (Vienna/Berlin/Munich)')
    ->addRadioButton('Revision (Halle)', ' Revision (Halle)')
    ->addRadioButton('Validated', ' Validated');

$form->addField('s2', STATIC_TEXT, '<br>');

$form->addField('separator_1', STATIC_TEXT, '<hr>');
$form->addField('was_prisoner_assistant', RADIO, '', 'victim only')
    ->setLabel('Was Person Victim <br> and / or <br> Prisoner Assistant <br> of Experiment(s)')
    ->addRadioButton('prisoner assistant only', ' prisoner assistant only')
    ->addRadioButton('prisoner assistant AND victim', ' prisoner assistant AND victim')
    ->addRadioButton('victim only', ' victim only');
$form->addField('separator_2', STATIC_TEXT, '<hr>');
$form->addField('gender', SELECT)
    ->setLabel('Gender')
    ->addOption(NO_VALUE, 'please choose')
    ->addOption('female')
    ->addOption('male');
$form->addField('birth_day', TEXT, 2)
    ->setLabel('Birth DMYYYY')
    ->addCondition(VALUE, MIN, 0)
    ->addCondition(VALUE, MAX, 31);
$form->addField('birth_month', TEXT, 2)
    ->addCondition(VALUE, MIN, 0)
    ->addCondition(VALUE, MAX, 12)
    ->appendTo('birth_day');
$form->addField('birth_year', TEXT, 4)
    ->addCondition(VALUE, MIN, 0)
    ->addCondition(VALUE, MAX, 1950)
    ->appendTo('birth_day');
$form->addField('twin', CHECKBOX, -1)
    ->setLabel('Twin');
$form->addField ('birth_place', TEXT, 250)
    ->setClass('keyboardInput')
    ->setLabel('Birth Place');
$form->addField('ID_birth_country', SELECT)
    ->setLabel('Birth Country')
    ->addOption(NO_VALUE, 'please choose')
    ->addOptionsFromTable('nmv__country', 'ID_country', 'country');
$form->addField('death_day', TEXT, 2)
    ->setLabel('Death DMYYYY')
    ->addCondition(VALUE, MIN, 0)
    ->addCondition(VALUE, MAX, 31);
$form->addField('death_month', TEXT, 2)
    ->addCondition(VALUE, MIN, 0)
    ->addCondition(VALUE, MAX, 12)
    ->appendTo('death_day');
$form->addField('death_year', TEXT, 4)
    ->addCondition(VALUE, MIN, 0)
    ->addCondition(VALUE, MAX, 2099)
    ->appendTo('death_day');
$form->addField('death_place', TEXT, 250)
    ->setClass('keyboardInput')
    ->setLabel('Death Place');
$form->addField('ID_death_institution', SELECT)
    ->setLabel('Death Institution (camp, clinic...)')
    ->addOption(NO_VALUE, 'please choose')
    ->addOptionsFromTable('nmv__institution', 'ID_institution', 'institution_name', 'ID_institution_type NOT IN (23, 24, 18, 19, 1)');
$form->addField('death_institution', TEXT, 255)
    ->setLabel(' ');
$form->addField('ID_death_country', SELECT)
    ->setLabel('Death Country')
    ->addOption(NO_VALUE, 'please choose')
    ->addOptionsFromTable('nmv__country', 'ID_country', 'country');
$form->addField('cause_of_death', TEXT, 255)
    ->setLabel('Cause of Death');
$form->addField('ID_marital_family_status', SELECT)
    ->setLabel('Marital Familiy Status')
    ->addOption(NO_VALUE, 'please choose')
    ->addOptionsFromTable('nmv__marital_family_status', 'ID_marital_family_status', 'marital_family_status');
$form->addField('ID_education', SELECT)
    ->setLabel('Highest Education Level')
    ->addOption(NO_VALUE, 'please choose')
    ->addOptionsFromTable('nmv__education', 'ID_education', 'education');
$form->addField('ID_occupation', SELECT)
    ->setLabel('Occupation')
    ->addOption(NO_VALUE, 'please choose')
    ->addOptionsFromTable('nmv__occupation', 'ID_occupation', 'occupation');
$form->addField('occupation_details', TEXT, 50)
    ->setClass('keyboardInput')
    ->setLabel('Occupation Details');
$form->addField('ID_religion', SELECT)
    ->setLabel('Religion')
    ->addOption(NO_VALUE, 'please choose')
    ->addOptionsFromTable('nmv__religion', 'ID_religion', 'religion');
$form->addField('ID_nationality_1938', SELECT)
    ->setLabel('Nationality (1938)')
    ->addOption(NO_VALUE, 'please choose')
    ->addOptionsFromTable('nmv__nationality', 'ID_nationality', 'nationality');
$form->addField('ID_ethnic_group', SELECT)
    ->setLabel('Ascribed Ethnic Group')
    ->addOption(NO_VALUE, 'please choose')
    ->addOptionsFromTable('nmv__ethnic_group', 'ID_ethnic_group', 'ethnic_group');
$form->addField('notes', TEXTAREA)
    ->setClass('keyboardInput')
    ->setLabel('Notes');
$form->addField('internal_notes', TEXTAREA)
    ->setLabel('Internal notes');
$form->addField('stolperstein_exists', CHECKBOX, -1)
    ->setLabel('Stolperstein exists');
$form->addField('photo_exists', CHECKBOX, -1)
    ->setLabel('Photo exists');
$form->addField('notes_photo', TEXTAREA)
    ->setLabel('Notes about Photo');

// Arrests group
$form->addField('arrest_prehistory', TEXTAREA)
    ->setClass('keyboardInput')
    ->setLabel('Arrest Prehistory');
$form->addField('arrest_location', TEXT, 255)
    ->setClass('keyboardInput')
    ->setLabel('Arrest Location');
$form->addField('ID_arrest_country', SELECT)
    ->setLabel('Arrest Country')
    ->addOption(NO_VALUE, 'please choose')
    ->addOptionsFromTable('nmv__country', 'ID_country', 'country');
$form->addField('arrest_history', TEXTAREA)
    ->setClass('keyboardInput')
    ->setLabel('Arrest History');


//complete db d
// After '45 group
if(!($dbi->checkUserPermission('mpg')))	{
    $form->addField('consequential_injuries', TEXTAREA)
        ->setLabel('Consequential injuries');
    $form->addField('residence_after_1945_place', TEXT, 250)
        ->setClass('keyboardInput')
        ->setLabel('Residence after \'45 (Place)');
    $form->addField('residence_after_1945_country', TEXT, 250)
        ->setLabel('Residence after \'45 (Country)');
    $form->addField('occupation_after_1945', TEXT, 50)
        ->setLabel('Occupation after \'45');
    $form->addField('ID_nationality_after_1945', SELECT)
        ->setLabel('Nationality after 1945')
        ->addOption(NO_VALUE, 'please choose')
        ->addOptionsFromTable('nmv__nationality', 'ID_nationality', 'nationality');
    $form->addField('notes_after_1945', TEXTAREA)
        ->setClass('keyboardInput')
        ->setLabel('Notes on life after 1945');
    $form->addField('compensation', RADIO, '', 'not specified')
        ->setLabel('Compensation')
        ->addRadioButton('yes', ' yes')
        ->addRadioButton('no', ' no')
        ->addRadioButton('not applicable', ' not applicable')
        ->addRadioButton('not specified', ' not specified');
    $form->addField('compensation_details', TEXTAREA)
        ->setClass('keyboardInput')
        ->setLabel('Compensation details');
}

$form->addField('ID_evaluation_status', SELECT)
    ->setLabel('Evaluation status')
    ->addOption(NO_VALUE, 'please choose')
    ->addOptionsFromTable('nmv__victim_evaluation_status', 'ID_evaluation_status', 'status');
$form->addField('status_due_to', TEXTAREA)
    ->setClass('keyboardInput')
    ->setLabel('Status due to');
$form->addField('status_notes', TEXTAREA)
    ->setClass('keyboardInput')
    ->setLabel('Status notes');

$form
    ->addButton(SUBMIT)
    ->addButton(APPLY);

$form
    ->addAction(DATABASE,'nmv__victim')
    ->addAction(REDIRECT,'nmv_view_victim?ID_victim={ID_victim}');

$dbi->addBreadcrumb(L_CONTENTS, 'z_menu_contents');
$dbi->addBreadcrumb('Victims', 'nmv_list_victims');


$layout
    ->set('title', getUrlParameter('ID_victim') ? 'Edit' . (getUrlParameter('type')  == 'prisoner_assistant' ? ' Prisoner Assistant' : ' Victim') : 'New' . (getUrlParameter('type')  == 'prisoner_assistant' ? ' Prisoner Assistant' : ' Victim'))
    ->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
    ->cast();
