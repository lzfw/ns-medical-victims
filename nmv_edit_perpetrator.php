<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission('edit');

$form = new Form('nmv_edit_perpetrator');

$form
	->setLabel('Perpetrator');

$form
	->addConnection(MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_perpetrator');
$form->addField('ID_perpetrator', PROTECTED_TEXT)
    ->setLabel('Perpetrator ID');
$form->addField('surname',TEXT,255,REQUIRED)
    ->setClass('keyboardInput')
    ->setLabel('Surname');
$form->addField('separator_0', STATIC_TEXT, '<hr>');
$form->addField('visibility', RADIO, '', '')
    ->setLabel('Visibility')
    ->addRadioButton('public', ' public (visible for every website-visitor')
    ->addRadioButton('restricted', ' restricted (visible on the website after login)')
    ->addRadioButton('hidden', ' hidden (not visible on website)');
$form->addField('separator_1', STATIC_TEXT, '<hr>');
$form->addField('first_names',TEXT,50)
    ->setClass('keyboardInput')
    ->setLabel('First Names');
$form->addField('titles',TEXT,50)
    ->setClass('keyboardInput')
    ->setLabel('Titles');
$form->addField('mpg_project', CHECKBOX, -1)
		->setLabel('MPG-Project');
$form->addField('birth_day',TEXT,2)
    ->setLabel('Birth DMYYYY')
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,31);
$form->addField('birth_month',TEXT,2)
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,12)
    ->appendTo('birth_day');
$form->addField('birth_year',TEXT,4)
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,1950)
    ->appendTo('birth_day');
$form->addField('birth_place',TEXT,200)
    ->setClass('keyboardInput')
    ->setLabel('Birth Place');
$form->addField('ID_birth_country', SELECT)
		->setLabel('Birth Country')
		->addOption(NO_VALUE, 'please choose')
		->addOptionsFromTable('nmv__country', 'ID_country', 'country');
$form->addField('death_day',TEXT,2)
    ->setLabel('Death DMYYYY')
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,31);
$form->addField('death_month',TEXT,2)
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,12)
    ->appendTo('death_day');
$form->addField('death_year',TEXT,4)
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,2099)
    ->appendTo ('death_day');
$form->addField('death_place',TEXT,200)
    ->setClass('keyboardInput')
    ->setLabel('Death Place');
$form->addField('ID_death_country', SELECT)
		->setLabel('Death Country')
		->addOption(NO_VALUE, 'please choose')
		->addOptionsFromTable('nmv__country', 'ID_country', 'country');
$form->addField('gender',SELECT)
    ->setLabel('Gender')
    ->addOption(NO_VALUE,'please choose')
    ->addOption('female')
    ->addOption('male');
$form->addField('ID_religion',SELECT)
    ->setLabel('Religion')
    ->addOption(NO_VALUE,'please choose')
    ->addOptionsFromTable('nmv__religion', 'ID_religion', 'religion');
$form->addField('ID_nationality_1938',SELECT)
    ->setLabel('Nationality (1938)')
    ->addOption(NO_VALUE,'please choose')
    ->addOptionsFromTable( 'nmv__nationality', 'ID_nationality', 'nationality');
$form->addField('occupation',TEXT,500)
    ->setLabel('Occupation');
$form->addField('ID_perp_class',SELECT)
    ->setLabel('Classification')
    ->addOption(NO_VALUE,'please choose')
    ->addOptionsFromTable('nmv__perpetrator_classification', 'ID_perp_class', 'classification');
$form->addField('career_history',TEXTAREA)
    ->setClass('keyboardInput')
    ->setLabel('Career History');
$form->addField('leopoldina_member',CHECKBOX,-1)
    ->setLabel('Leopoldina Member');
$form->addField('leopoldina_since_when', TEXT, 4)
		->setLabel('Since (Year)')
		->addCondition(VALUE,MIN,1800)
    ->addCondition(VALUE,MAX,2045)
		->appendTo('leopoldina_member');
$form->addField('nsdap_member',CHECKBOX,-1)
    ->setLabel('NSDAP Member');
$form->addField('nsdap_since_when',TEXT,4)
    ->setLabel('Since (Year)')
    ->addCondition(VALUE,MIN,1800)
    ->addCondition(VALUE,MAX,1945)
    ->appendTo('nsdap_member');
$form->addField('ss_member',CHECKBOX,-1)
    ->setLabel('SS Member');
$form->addField('ss_since_when',TEXT,4)
    ->setLabel('Since (Year)')
    ->addCondition(VALUE,MIN,1800)
    ->addCondition(VALUE,MAX,1945)
    ->appendTo('ss_member');
$form->addField('sa_member',CHECKBOX,-1)
    ->setLabel('SA Member');
$form->addField('sa_since_when',TEXT,4)
    ->setLabel('Since (Year)')
    ->addCondition(VALUE,MIN,1800)
    ->addCondition(VALUE,MAX,1945)
    ->appendTo('sa_member');
$form->addField('other_nsdap_organisations_member',CHECKBOX,-1)
    ->setLabel('Other NS Org. Member');
$form->addField('details_all_memberships',TEXTAREA)
    ->setLabel('Membership Details');
$form->addField('career_after_1945',TEXTAREA)
    ->setClass('keyboardInput')
    ->setLabel('Career after 1945');
$form->addField('prosecution',TEXT,500)
    ->setClass('keyboardInput')
    ->setLabel('Prosecution');
$form->addField('prison_time',TEXT,255)
    ->setLabel('Prison Time');
$form->addField('notes',TEXTAREA)
    ->setClass('keyboardInput')
    ->setLabel('Notes');

$form
	->addButton(SUBMIT)
	->addButton(APPLY);

$form
	->addAction(DATABASE,'nmv__perpetrator')
	->addAction(REDIRECT,'nmv_view_perpetrator?ID_perpetrator={ID_perpetrator}');

$dbi->addBreadcrumb(L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb('Perpetrators','nmv_list_perpetrators');

$layout
	->set('title',getUrlParameter('ID_perpetrator') ? 'Edit Perpetrator' : 'New Perpetrator')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
