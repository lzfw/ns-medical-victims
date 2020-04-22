<?php
/**
*creates search form
*
*
*
*/

// CMS file: search form (public)
// last known update: 2013-01-22

require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

// url parameters
$dbi->setUserVar ('view',getUrlParameter('view'),'default');

include_once './search_forms/nmv_search_victims_variable.php';

// victim search form
$victimForm = new Form ('search_victim','nmv_result_victims.php','GET');

$victimForm->addConnection(MYSQL_DB,$db_host,$db_user,$db_pass,$db_name);

$victimForm->addField ('ID_victim',TEXT,5)
	->setLabel ('ID');

$victimForm->addField ('surname',TEXT,120)
    ->setClass ('keyboardInput')
	->setLabel ('Surname');

$victimForm->addField ('first_names',TEXT,120)
    ->setClass ('keyboardInput')
	->setLabel ('First Name(s)');

$victimForm
	->addButton (BACK)
	->addButton (RESET)
	->addButton (SUBMIT,L_SEARCH);


// victim from MPG project search form
$MPGvictimForm = new Form ('search_mpg_victim','nmv_result_mpg_victims.php','GET');

$MPGvictimForm->addConnection(MYSQL_DB,$db_host,$db_user,$db_pass,$db_name);

$MPGvictimForm->addField ('ID_victim',TEXT,5)
	->setLabel ('ID');

$MPGvictimForm->addField ('surname',TEXT,120)
    ->setClass ('keyboardInput')
	->setLabel ('Surname');

$MPGvictimForm->addField ('first_names',TEXT,120)
    ->setClass ('keyboardInput')
	->setLabel ('First Name(s)');

$MPGvictimForm->addField ('ID_dataset_origin',SELECT)
	    ->setLabel ('MPG Project Data from')
	    ->addOption (NO_VALUE,'all workgroups')
	    ->addOptionsFromTableOrderedById ( 'nmv__dataset_origin', 'ID_dataset_origin', 'work_group');

$MPGvictimForm
	->addButton (BACK)
	->addButton (RESET)
	->addButton (SUBMIT,L_SEARCH);


// victimgroups from MPG project search form
$MPGgroupForm = new Form ('search_mpg_group','nmv_result_mpg_group.php','GET');

$MPGgroupForm->addConnection(MYSQL_DB,$db_host,$db_user,$db_pass,$db_name);

$MPGgroupForm->addField ('cause_of_death', CHECKBOX, -1)
	->setLabel ('Cause of Death: executed');

$MPGgroupForm->addField ('prisoner_of_war', CHECKBOX, -1)
	->setLabel ('Imprisonment: Prisoner of War');

$MPGgroupForm->addField ('psychiatric_patient', CHECKBOX, -1)
	->setLabel ('Imprisonment: Psychiatric Patient');

$MPGgroupForm->addField ('ID_institution',SELECT)
	    ->setLabel ('Institution')
	    ->addOption (NO_VALUE,'all institutions')
	    ->addOptionsFromTableOrderedById ( 'nmv__institution', 'ID_institution', 'institution_name', 'ID_institution IN (67, 94, 97, 106, 113, 114, 122)');

$MPGgroupForm->addField ('ID_dataset_origin',SELECT)
	    ->setLabel ('MPG Project Data from')
	    ->addOption (NO_VALUE,'all workgroups')
	    ->addOptionsFromTableOrderedById ( 'nmv__dataset_origin', 'ID_dataset_origin', 'work_group');



$MPGgroupForm
	->addButton (BACK)
	->addButton (RESET)
	->addButton (SUBMIT,L_SEARCH);

// perpetrator search form
$perpetratorForm =
    new Form ('search_perpetrator','nmv_result_perpetrators.php','GET');

$perpetratorForm->addField ('ID_perpetrator',TEXT,5)
	->setLabel ('ID');

$perpetratorForm->addField ('surname',TEXT,120)
    ->setClass ('keyboardInput')
	->setLabel ('Surname');

$perpetratorForm->addField ('first_names', TEXT,120)
    ->setClass ('keyboardInput')
	->setLabel ('First Names');

$perpetratorForm
	->addButton (SUBMIT,L_SEARCH);

// experiment search form
$experimentsForm =
    new Form ('search_experiments','nmv_result_experiments.php','GET');

$experimentsForm->addConnection(MYSQL_DB,$db_host,$db_user,$db_pass,$db_name);

$experimentsForm->addField ('ID_experiment',TEXT,5)
	->setLabel ('ID');

$experimentsForm->addField ('experiment_title',TEXT,255)
    ->setClass ('keyboardInput')
	->setLabel ('Biomedical Research Title');

$experimentsForm->addField ('funding',TEXT,255)
    ->setClass ('keyboardInput')
	->setLabel ('Funding');

$experimentsForm->addField ('field_of_interest',TEXT,50)
    ->setClass ('keyboardInput')
	->setLabel ('Field of Interest');

$experimentsForm->addField ('objective',TEXT,50)
    ->setClass ('keyboardInput')
	->setLabel ('Objective');

$experimentsForm->addField ('surname', TEXT, 50)
		->setClass ('keyboardInput')
	->setLabel ('Surname Perpetrator');

$experimentsForm->addField ('ID_institution', SELECT)
		->setLabel ('Institution')
		->addOption (NO_VALUE,'all institutions')
		->addOptionsFromTable	 ( 'nmv__institution', 'ID_institution', 'institution_name');

$experimentsForm
	->addButton (SUBMIT,L_SEARCH);

// literature search form
$literatureForm =
    new Form ('search_literature','nmv_result_literature.php','GET');

$literatureForm->addField ('ID_literature',TEXT,5)
	->setLabel ('ID');

$literatureForm->addField ('lit_title',TEXT,250)
    ->setClass ('keyboardInput')
	->setLabel ('Title');

$literatureForm->addField ('authors',TEXT,50)
    ->setClass ('keyboardInput')
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
    ->setClass ('keyboardInput')
	->setLabel ('Title');

$sourceForm->addField ('signature',TEXT,50)
    ->setClass ('keyboardInput')
	->setLabel ('Signature');

$sourceForm->addField ('description',TEXT,255)
    ->setClass ('keyboardInput')
	->setLabel ('Description');

$sourceForm
	->addButton (SUBMIT,L_SEARCH);

// layout
$layout
	->set('title',L_SEARCH)
	->set('content',
	    '<div class="block">
					<h3>Search Victims by name or ID</h3>
					<p>Search in complete database.
					<br> If more than one field is filled in, the search returns only results that match <strong>all</strong> those fields</p>' .
			    ($dbi->checkUserPermission('view') ? $victimForm->run() : 'In order to search victims, <a href="/z_login">please log in</a>.') .
			'</div>
			 <div class="block">
					<h3>Search Victims (MPG Project) by name or ID</h3>
					<p>Search in MPG-project-datasets.
					<br>If more than one field is filled in, the search returns only results that match <strong>all</strong> those fields
					<br>If a combination of workgroups is selected, the result shows only datasets that contain data of all these workgroups.</p>' .
					($dbi->checkUserPermission('view') ? $MPGvictimForm->run() : 'In order to search MPG-project-victims, <a href="/z_login">please log in</a>.') .
			'</div>
			<div class="block">
				<h3>Filter Victimgroups (MPG Project)</h3>
					<p>Shows fliltered list of victims in MPG-project-datasets.
					<br>Returns victims that match <strong>all</strong> given criteria</p>' .
					($dbi->checkUserPermission('view') ? $MPGgroupForm->run() : 'In order to search MPG-project-victims, <a href="/z_login">please log in</a>.') .
			'</div>
			<div class="block">
					<h3>Search Perpetrators by name or ID</h3>
					<p>If more than one field is filled in, the search returns only results that match <strong>all</strong> those fields</p>' .
			    ($dbi->checkUserPermission('view') ? $perpetratorForm->run() : 'In order to search perpetrators, <a href="/z_login">please log in</a>.') .
	    '</div>
			<div class="block">
					<h3>Search Biomedical Research (Experiments)</h3>
					<p>If more than one field is filled in, the search returns only results that match <strong>all</strong> those fields</p>' .
			    ($dbi->checkUserPermission('view') ? $experimentsForm->run() : 'In order to search biomedical research, <a href="/z_login">please log in</a>.') .
	    '</div>
			<div class="block">
					<h3>Search Literature</h3>
					<p>If more than one field is filled in, the search returns only results that match <strong>all</strong> those fields</p>' .
			    ($dbi->checkUserPermission('view') ? $literatureForm->run() : 'In order to search literature, <a href="/z_login">please log in</a>.') .
	    '</div>
			<div class="block">
					<h3>Search Sources</h3>
					<p>If more than one field is filled in, the search returns only results that match <strong>all</strong> those fields</p>' .
			    ($dbi->checkUserPermission('view') ? $sourceForm->run() : 'In order to search sources, <a href="/z_login">please log in</a>.') .
			'</div>'		)
	->set('sidebar','<h3>'.L_HELP.'</h3>'.$dbi->getTextblock_HTML ('search'))
	->cast();

?>
