<?php
// CMS file: search form (public)
// last known update: 2013-01-22

require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

// url parameters
$dbi->setUserVar ('view',getUrlParameter('view'),'default');

// victim search form
$victimForm = new Form ('search_victim','nmv_result_victims.php','GET');

$victimForm->addField ('ID_victim',TEXT,5)
	->setLabel ('ID');

$victimForm->addField ('surname',TEXT,120)
	->setLabel ('Surname');

$victimForm->addField ('first_names',TEXT,120)
	->setLabel ('First Names');

$victimForm
	->addLabel ('Filter');

$victimForm->addField ('mpg_project', CHECKBOX, -1)
	->setLabel ('MPG Project');

$victimForm
	->addButton (BACK)
	->addButton (RESET)
	->addButton (SUBMIT,L_SEARCH);

// perpetrator search form
$perpetratorForm =
    new Form ('search_perpetrator','nmv_result_perpetrators.php','GET');

$perpetratorForm->addField ('ID_perpetrator',TEXT,5)
	->setLabel ('ID');

$perpetratorForm->addField ('surname',TEXT,120)
	->setLabel ('Surname');

$perpetratorForm->addField ('first_names', TEXT,120)
	->setLabel ('First Names');

$perpetratorForm
	->addButton (SUBMIT,L_SEARCH);

// experiment search form
$experimentsForm =
    new Form ('search_experiments','nmv_result_experiments.php','GET');

$experimentsForm->addField ('ID_experiment',TEXT,5)
	->setLabel ('ID');

$experimentsForm->addField ('experiment_title',TEXT,255)
	->setLabel ('Biomedical Research Title');

$experimentsForm->addField ('funding',TEXT,255)
	->setLabel ('Funding');

$experimentsForm->addField ('field_of_interest',TEXT,50)
	->setLabel ('Field of Interest');

$experimentsForm->addField ('objective',TEXT,50)
	->setLabel ('Objective');

$experimentsForm
	->addButton (SUBMIT,L_SEARCH);

// literature search form
$literatureForm =
    new Form ('search_literature','nmv_result_literature.php','GET');

$literatureForm->addField ('ID_literature',TEXT,5)
	->setLabel ('ID');

$literatureForm->addField ('lit_title',TEXT,250)
	->setLabel ('Title');

$literatureForm->addField ('authors',TEXT,50)
	->setLabel ('Authors');

$literatureForm->addField ('lit_year',TEXT,20)
	->setLabel ('Year');

$literatureForm
	->addButton (SUBMIT,L_SEARCH);

// source search form
$sourceForm =
    new Form ('search_source','nmv_result_source.php','GET');

$sourceForm->addField ('ID_source',TEXT,5)
	->setLabel ('ID');

$sourceForm->addField ('source_title',TEXT,255)
	->setLabel ('Title');

$sourceForm->addField ('signature',TEXT,50)
	->setLabel ('Signature');

$sourceForm->addField ('description',TEXT,255)
	->setLabel ('Description');

$sourceForm
	->addButton (SUBMIT,L_SEARCH);

// layout
$layout
	->set('title',L_SEARCH)
	->set('content',
	    '<h3>Victims</h3>' .
	    ($dbi->checkUserPermission('view') ? $victimForm->run() : 'In order to search victims, <a href="/z_login">please log in</a>.') .
	    '<h3>Perpetrators</h3>' .
	    ($dbi->checkUserPermission('view') ? $perpetratorForm->run() : 'In order to search perpetrators, <a href="/z_login">please log in</a>.') .
	    '<h3>Biomedical Research</h3>' .
	    ($dbi->checkUserPermission('view') ? $experimentsForm->run() : 'In order to search biomedical research, <a href="/z_login">please log in</a>.') .
	    '<h3>Literature</h3>' .
	    ($dbi->checkUserPermission('view') ? $literatureForm->run() : 'In order to search literature, <a href="/z_login">please log in</a>.') .
	    '<h3>Sources</h3>' .
	    ($dbi->checkUserPermission('view') ? $sourceForm->run() : 'In order to search sources, <a href="/z_login">please log in</a>.'))
	->set('sidebar','<h3>'.L_HELP.'</h3>'.$dbi->getTextblock_HTML ('search'))
	->cast();

?>
