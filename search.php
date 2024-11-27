<?php
/**
*creates search page
*
*
*
*/

// CMS file: search form (public)
// last known update: 2013-01-22

require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

// url parameters
$dbi->setUserVar('view',getUrlParameter('view'),'default');

include_once './search_forms/nmv_search_victims_filter.php';
include_once './search_forms/nmv_search_mpg_filter.php';
include_once './search_forms/nmv_search_diagnoses.php';
include_once './search_forms/nmv_search_perpetrators_filter.php';
include_once './search_forms/nmv_search_vic_exp_filter.php';

// victim search form
$victimForm = new Form('search_victim','nmv_result_victims.php','GET');

$victimForm->addConnection(MYSQL_DB,$db_host,$db_user,$db_pass,$db_name);

$victimForm->addField('ID_victim',TEXT,10)
	->setLabel('ID');

$victimForm->addField('surname',TEXT,120)
    ->setClass('keyboardInput')
	->setLabel('Surname');

$victimForm->addField('first_names',TEXT,120)
    ->setClass('keyboardInput')
	->setLabel('First Name(s)');

$victimForm->addField('openUid',TEXT,20)
	->setLabel('openUid -- temporary for data-transfer');

$victimForm->addField('uid',TEXT,20)
	->setLabel('Uid -- temporary for data-transfer');


$victimForm
	->addButton(BACK)
	->addButton(RESET)
	->addButton(SUBMIT,L_SEARCH);


// victim from MPG project search form
$MPGvictimForm = new Form('search_mpg_victim','nmv_result_mpg_victims.php','GET');

$MPGvictimForm->addConnection(MYSQL_DB,$db_host,$db_user,$db_pass,$db_name);

$MPGvictimForm->addField('ID_victim',TEXT,5)
	->setLabel('ID');

$MPGvictimForm->addField('surname',TEXT,120)
    ->setClass('keyboardInput')
	->setLabel('Surname');

$MPGvictimForm->addField('first_names',TEXT,120)
    ->setClass('keyboardInput')
	->setLabel('First Name(s)');

$MPGvictimForm->addField('ID_dataset_origin',SELECT)
	    ->setLabel('MPG Project Data from')
	    ->addOption(NO_VALUE,'all workgroups')
	    ->addOptionsFromTableOrderedById('nmv__dataset_origin', 'ID_dataset_origin', 'work_group');

$MPGvictimForm->addField('openUid',TEXT,20)
	->setLabel('openUid -- temporary for data-transfer');

$MPGvictimForm->addField('uid',TEXT,20)
	->setLabel('Uid -- temporary for data-transfer');

$MPGvictimForm
	->addButton(BACK)
	->addButton(RESET)
	->addButton(SUBMIT,L_SEARCH);


// prisoner assistant search form
$assistantForm = new Form('search_assitant','nmv_result_prisoner_assistant.php','GET');

$assistantForm->addConnection(MYSQL_DB,$db_host,$db_user,$db_pass,$db_name);

$assistantForm->addField('ID_victim',TEXT,5)
	->setLabel('ID');

$assistantForm->addField('surname',TEXT,120)
    ->setClass('keyboardInput')
	->setLabel('Surname');

$assistantForm->addField('first_names',TEXT,120)
    ->setClass('keyboardInput')
	->setLabel('First Name(s)');

$assistantForm
	->addButton(BACK)
	->addButton(RESET)
	->addButton(SUBMIT,L_SEARCH);




// perpetrator search form
$perpetratorForm =
    new Form('search_perpetrator','nmv_result_perpetrators.php','GET');

$perpetratorForm->addField('ID_perpetrator',TEXT,5)
	->setLabel('ID');

$perpetratorForm->addField('surname',TEXT,120)
    ->setClass('keyboardInput')
	->setLabel('Surname');

$perpetratorForm->addField('first_names', TEXT,120)
    ->setClass('keyboardInput')
	->setLabel('First Names');

$perpetratorForm
	->addButton(SUBMIT,L_SEARCH);

// experiment search form
$experimentsForm =
    new Form('search_experiments','nmv_result_experiments.php','GET');

$experimentsForm->addConnection(MYSQL_DB,$db_host,$db_user,$db_pass,$db_name);

$experimentsForm->addField('ID_experiment',TEXT,5)
	->setLabel('ID');

$experimentsForm->addField('experiment_title',TEXT,255)
    ->setClass('keyboardInput')
	->setLabel('Biomedical Research Title');

$experimentsForm->addField('funding',TEXT,255)
    ->setClass('keyboardInput')
	->setLabel('Funding');

$experimentsForm->addField('ID_foi',SELECT)
	->setLabel('Field of Interest')
	->addOption(NO_VALUE, 'all fields of interest')
	->addOptionsFromTable('nmv__field_of_interest', 'ID_foi', 'field_of_interest');

$experimentsForm->addField('objective',TEXT,50)
    ->setClass('keyboardInput')
	->setLabel('Objective');

$experimentsForm->addField('surname', TEXT, 50)
		->setClass('keyboardInput')
	->setLabel('Surname Perpetrator');

$experimentsForm->addField('ID_exp_classification', SELECT)
		->setLabel('Classification')
		->addOption(NO_VALUE, 'all classifications')
		->addOptionsFromTable('nmv__experiment_classification', 'ID_exp_classification', 'classification');

$experimentsForm->addField('ID_institution', SELECT)
		->setLabel('Institution')
		->addOption(NO_VALUE,'all institutions')
		->addOptionsFromTable('nmv__institution', 'ID_institution', "CONCAT(IFNULL(institution_name, '-/-'), '&emsp;---&emsp;', LEFT(IFNULL(location, '-/-'), 50))",
															'EXISTS (	SELECT *
																				FROM nmv__experiment_institution
																				WHERE nmv__institution.ID_institution = nmv__experiment_institution.ID_institution)');



$experimentsForm
	->addButton(SUBMIT,L_SEARCH);

// literature search form
$literatureForm =
    new Form('search_literature','nmv_result_literature.php','GET');

$literatureForm->addField('ID_literature',TEXT,5)
	->setLabel('ID');

$literatureForm->addField('lit_title',TEXT,250)
    ->setClass('keyboardInput')
	->setLabel('Title');

$literatureForm->addField('authors',TEXT,50)
    ->setClass('keyboardInput')
	->setLabel('Authors');

$literatureForm->addField('lit_year',TEXT,20)
	->setLabel('Year');

$literatureForm
	->addButton(SUBMIT,L_SEARCH);

// source search form
$sourceForm =
    new Form('search_source','nmv_result_source.php','GET');
$sourceForm->addConnection(MYSQL_DB,$db_host,$db_user,$db_pass,$db_name);


$sourceForm->addField('ID_source',TEXT,5)
	->setLabel('ID');

$sourceForm->addField('source_title',TEXT,255)
    ->setClass('keyboardInput')
	->setLabel('Title');

$sourceForm->addField('ID_institution', SELECT)
		->setLabel('Location (Archive, Museum, <br> Library, Academic Institution)')
		->addOption(NO_VALUE, 'all institutions')
		->addOptionsFromTable('nmv__institution', 'ID_institution', 'institution_name', 'ID_institution_type IN (1, 18, 23, 24)');

$sourceForm->addField('signature',TEXT,50)
    ->setClass('keyboardInput')
	->setLabel('Signature');

$sourceForm->addField('description',TEXT,255)
    ->setClass('keyboardInput')
	->setLabel('Description');

$sourceForm
	->addButton(SUBMIT,L_SEARCH);

//complete db d
//different contents depending on mpg-permission
if ($dbi->checkUserPermission('mpg')) :
	$victim_form = '';
	$experiments_form = '';
else :
	$victim_form =
		'<div class="relative">
					<input class="hide_show_checkbox"  id="checkbox_search_victim" type="checkbox" checked="checked">
					<label class="hide_show_label" id="label_search_victim" for="checkbox_search_victim">Search - Victim</label>
					<div class="hide_show_element block" id="element_search_victim">
							<p>Search in complete database.
							<br> If more than one field is filled in, the search returns only results that match <strong>all</strong> those fields</p>' .
					    ($dbi->checkUserPermission('view') ? $victimForm->run() : 'In order to search victims, <a href="/z_login">please log in</a>.') .
					'</div>
			</div>';
		$experiments_form =
		'<div class="relative">
				<input class="hide_show_checkbox"  id="checkbox_search_experiment" type="checkbox" checked="checked">
				<label class="hide_show_label" id="label_search_experiment" for="checkbox_search_experiment">Search - Biomedical Research (Experiment)</label>
				<div class="hide_show_element block" id="element_search_experiment">
						<p>If more than one field is filled in, the search returns only results that match <strong>all</strong> those fields
						<br>Text fields: it is sufficient to enter a part of the title, name.... The search returns all results, that contain the search term.
						</p>' .
						($dbi->checkUserPermission('view') ? $experimentsForm->run() : 'In order to search biomedical research, <a href="/z_login">please log in</a>.') .
				'</div>
		</div>
		';
endif;


// layout
$layout
	->set('title','Search and Filters')
	->set('content',
			'<div class="relative">
					<div class="hide_show_element block" id="element_search_tips">
					<p>Search is not case sensitive<p>
					<h3>wildcard character *</h3>
					<p>	
						You can use * as a wildcard-character. It replaces one or many characters.
						<br>&nbsp; &rarr; Smi* returns all results that begin with Smi (e.g Smith, Smidt, Smilla ...)
						<br>&nbsp; &rarr; J*zef returns results for Józef, Jozef, Joezef, Juzef ...
						<br>&nbsp; &rarr; * returns all results with an entry in the corresponding column.
					</p><br>
					<h3>show and hide forms</h3>
					<p>
						The different search- and filter-forms can be shown or hidden by clicking on the gray headline-areas.
					</p><br>
					<h3>multiple inputs</h3>
					<p>
						If more than one textfield or selection is used, the search or filter will return only results that match <strong> all </strong> those inputs.
					</p>
			</div>
			<br><br>
			<h2>Search for specific entries</h2>
			'. $victim_form . '
			<div class="relative">
					 <input class="hide_show_checkbox"  id="checkbox_search_mpg_victim" type="checkbox" checked="checked">
					 <label class="hide_show_label" id="label_search_mpg_victim" for="checkbox_search_mpg_victim">Search - Victim (MPG Project)</label>
					 <div class="hide_show_element block" id="element_search_mpg_victim">
							<p>Search in MPG-project-datasets.
							<br>If more than one field is filled in, the search returns only results that match <strong>all</strong> those fields
							<br>If a combination of workgroups is selected, the result shows only datasets that contain data of all these workgroups.</p>' .
							($dbi->checkUserPermission('view') ? $MPGvictimForm->run() : 'In order to search MPG-project-victims, <a href="/z_login">please log in</a>.') .
					'</div>
			</div>
			<div class="relative">
						<input class="hide_show_checkbox"  id="checkbox_search_pa" type="checkbox" checked="checked">
						<label class="hide_show_label" id="label_search_pa" for="checkbox_search_pa">Search - Prisoner Assistant</label>
						<div class="hide_show_element block" id="element_search_pa">
								<p>Search in complete database.
								<br> If more than one field is filled in, the search returns only results that match <strong>all</strong> those fields</p>' .
								($dbi->checkUserPermission('view') ? $assistantForm->run() : 'In order to search victims, <a href="/z_login">please log in</a>.') .
						'</div>
			</div>
			<div class="relative">
					<input class="hide_show_checkbox"  id="checkbox_search_perpetrator" type="checkbox" checked="checked">
					<label class="hide_show_label" id="label_search_perpetrator" for="checkbox_search_perpetrator">Search - Perpetrator</label>
					<div class="hide_show_element block" id="element_search_perpetrator">
							<p>If more than one field is filled in, the search returns only results that match <strong>all</strong> those fields</p>' .
					    ($dbi->checkUserPermission('view') ? $perpetratorForm->run() : 'In order to search perpetrators, <a href="/z_login">please log in</a>.') .
			    '</div>
			</div>
			'. $experiments_form .'
			<div class="relative">
					<input class="hide_show_checkbox"  id="checkbox_search_literature" type="checkbox" checked="checked">
					<label class="hide_show_label" id="label_search_literature" for="checkbox_search_literature">Search - Literature</label>
					<div class="hide_show_element block" id="element_search_literature">
							<p>If more than one field is filled in, the search returns only results that match <strong>all</strong> those fields</p>' .
					    ($dbi->checkUserPermission('view') ? $literatureForm->run() : 'In order to search literature, <a href="/z_login">please log in</a>.') .
			    '</div>
			</div>
			<div class="relative">
					<input class="hide_show_checkbox"  id="checkbox_search_source" type="checkbox" checked="checked">
					<label class="hide_show_label" id="label_search_source" for="checkbox_search_source">Search - Source</label>
					<div class="hide_show_element block" id="element_search_source">
							<p>If more than one field is filled in, the search returns only results that match <strong>all</strong> those fields</p>' .
					    ($dbi->checkUserPermission('view') ? $sourceForm->run() : 'In order to search sources, <a href="/z_login">please log in</a>.') .
					'</div>
			</div>

			<br><br>
			<h2>Filter</h2>
			<div class="relative">
					<input class="hide_show_checkbox"  id="checkbox_search_diagnoses" type="checkbox" checked="checked">
					<label class="hide_show_label" id="label_search_diagnoses" for="checkbox_search_diagnoses">Filter - Diagnoses</label>
					<div class="hide_show_element block" id="element_search_diagnoses">
					<br>Please use only one of both filters at the same time
					<hr>' .
							($dbi->checkUserPermission('view') ? $diagnosesForm->run() : 'In order to search victims, <a href="/z_login">please log in</a>.') .
					'</div>
			</div>
			<div class="relative">
					<input class="hide_show_checkbox"  id="checkbox_filter_mpg_victim" type="checkbox" checked="checked">
					<label class="hide_show_label" id="label_filter_mpg_victim" for="checkbox_filter_mpg_victim">Filter - Victims ("MPG Project - Groups")</label>
					<div class="hide_show_element block" id="element_filter_mpg_victim">
							<p>Shows fliltered list of victims in MPG-project-datasets.
							<br>Returns victims that match <strong>all</strong> given criteria
							<br>If a combination of workgroups is selected, the result shows only datasets that contain data of all these workgroups.</p>
							' .
							($dbi->checkUserPermission('view') ? $MPGfilterForm->run() : 'In order to search MPG-project-victims, <a href="/z_login">please log in</a>.') .
					'</div>
			</div>
			<div class="relative">
					<input class="hide_show_checkbox"  id="checkbox_filter_victim" type="checkbox" checked="checked">
					<label class="hide_show_label" id="label_filter_victim" for="checkbox_filter_victim">Filter - Victims</label>
					<div class="hide_show_element block" id="element_filter_victim">
							<br>If more than one filter is applied, the search only returns results that match <strong>all</strong> selected criteria
							<br><br></p>' .
							($dbi->checkUserPermission('view') ? $victimsVariableForm->run() : 'In order to search victims, <a href="/z_login">please log in</a>.') .
					'</div>
			</div>
			<div class="relative">
					<input class="hide_show_checkbox"  id="checkbox_filter_vic_exp" type="checkbox" checked="checked">
					<label class="hide_show_label" id="label_filter_vic_exp" for="checkbox_filter_vic_exp">Filter - Victim-Experiment</label>
					<div class="hide_show_element block" id="element_filter_vic_exp">
							<br>If more than one filter is applied, the search only returns results that match <strong>all</strong> selected criteria
							<br><br></p>' .
							($dbi->checkUserPermission('view') ? $victimExperimentForm->run() : 'In order to search victims, <a href="/z_login">please log in</a>.') .
					'</div>
			</div>
			<div class="relative">
					<input class="hide_show_checkbox"  id="checkbox_filter_perpetrator" type="checkbox" checked="checked">
					<label class="hide_show_label" id="label_filter_perpetrator" for="checkbox_filter_perpetrator">Filter - Perpetrators</label>
					<div class="hide_show_element block" id="element_filter_perpetrator">
							<br>If more than one filter is applied, the search only returns results that match <strong>all</strong> selected criteria
							<br><strong>keyword</strong>: returns all perpetrators where the search-text is part of the text in the respective column.
							<br><br></p>' .
							($dbi->checkUserPermission('view') ? $perpetratorsFilterForm->run() : 'In order to search perpetrators, <a href="/z_login">please log in</a>.') .
					'</div>
			</div>
			'		)
	//->set('sidebar','<h3>'.L_HELP.'</h3>'.$dbi->getTextblock_HTML ('search'))
	->cast();


?>
