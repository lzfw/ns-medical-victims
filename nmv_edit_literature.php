<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('edit');

$form = new Form ('nmv_edit_literature');

$form
	->setLabel('Literature');

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_literature');
$form->addField ('ID_literature', PROTECTED_TEXT)
    ->setLabel ('Literature ID');
$form->addField ('authors',TEXTAREA)
    ->setClass ('keyboardInput')
    ->setLabel ('Authors');
$form->addField ('lit_year',TEXT,50)
    ->setLabel ('Year');
$form->addField ('lit_title',TEXT,1500, REQUIRED)
    ->setClass ('keyboardInput')
    ->setLabel ('Title');

// article properties - should be grouped somehow
$form->addField ('article',CHECKBOX,-1)
    ->setLabel ('Is article');
$form->addField ('journal_or_series',TEXT,1500)
    ->setClass ('keyboardInput')
    ->setLabel ('Journal or Edited Volume');
$form->addField ('editor',TEXT,250)
    ->setClass ('keyboardInput')
    ->setLabel ('Editor');
$form->addField ('volume',TEXT,50)
    ->setLabel ('Volume');
$form->addField ('pages',TEXT,50)
    ->setLabel ('Pages');

$form->addField ('location',TEXT,250)
    ->setClass ('keyboardInput')
    ->setLabel ('Location');
$form->addField ('publisher',TEXT,250)
    ->setClass ('keyboardInput')
    ->setLabel ('Publisher');
$form->addField ('scientific_exploitation',CHECKBOX,1)
    ->setLabel ('<div class="tooltip">Scientific exploitation
                    <span class="tooltiptext">was this literature making use results of unethical research</span>
                </div>');

$form->addField ('written_by_perpetrator',CHECKBOX,-1)
    ->setLabel ('Written by perpetrator')
		->setDescription ('&rArr; Tick if at least one author is listed as perpetrator');
$form->addField ('notes',TEXTAREA)
    ->setClass ('keyboardInput')
    ->setLabel ('Notes');
$form->addField ('url',TEXTAREA)
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
$form->addField ('isbn',TEXT,25)
    ->setLabel ('ISBN');
$form->addField ('doi',TEXT,255)
    ->setLabel ('DOI');
$form->addField('visibility', RADIO, '', '')
    ->setLabel('Visibility')
    ->addRadioButton('public', ' public (visible for every website-visitor)')
    ->addRadioButton('restricted', ' restricted (visible on the website after login)')
    ->addRadioButton('hidden', ' hidden (not visible on website)');

$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__literature')
	->addAction (REDIRECT,'nmv_view_literature?ID_literature={ID_literature}');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Literature','nmv_list_literature');

$layout
	->set('title',getUrlParameter('ID_literature') ? 'Edit literature' : 'New literature')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
