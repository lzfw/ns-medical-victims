<?php
/**
* creates form for filter victims in mpg-project
*
*
*
*/


// victimgroups from MPG project search form
$MPGfilterForm = new Form('search_mpg_group','nmv_result_mpg_filter.php','GET');

$MPGfilterForm->addConnection(MYSQL_DB,$db_host,$db_user,$db_pass,$db_name);

$MPGfilterForm->addField('cause_of_death', CHECKBOX, -1)
	->setLabel('Cause of Death: executed');

$MPGfilterForm->addField('prisoner_of_war', CHECKBOX, -1)
	->setLabel('Imprisonment: Prisoner of War');

$MPGfilterForm->addField('psychiatric_patient', CHECKBOX, -1)
	->setLabel('Imprisonment: Psychiatric Patient');

$MPGfilterForm->addField('beddies_database', CHECKBOX, -1)
	->setLabel('Beddies Database');

$MPGfilterForm->addField('rothemund_list', CHECKBOX, -1)
	->setLabel('Rothemund List');

$MPGfilterForm->addField('schlote_list', CHECKBOX, -1)
	->setLabel('Schlote List');

$MPGfilterForm->addField('m_series', CHECKBOX, -1)
	->setLabel('M-Series');


$MPGfilterForm->addField('ID_institution', SELECT)
	    ->setLabel('Institution (Brain Report, <br> Hospitalisation, Experiment)')
	    ->addOption(NO_VALUE, 'all institutions')
	    ->addOptionsFromTable('nmv__institution', 'ID_institution', "CONCAT(IFNULL(institution_name, '-/-'), '&emsp;---&emsp;', LEFT(IFNULL(location, '-/-'), 50))",
				'EXISTS (SELECT * FROM nmv__med_history_brain
									WHERE nmv__institution.ID_institution = nmv__med_history_brain.ID_institution)
				OR EXISTS (SELECT * FROM nmv__med_history_hosp
				WHERE nmv__institution.ID_institution = nmv__med_history_hosp.ID_institution)
				OR EXISTS (SELECT * FROM nmv__victim_experiment ve
				LEFT JOIN nmv__experiment_institution ei ON ei.ID_experiment = ve.ID_experiment
				WHERE nmv__institution.ID_institution = ei.ID_institution)');

$MPGfilterForm->addField('ID_tissue_institution', SELECT)
		->setLabel('Institution (Tissue)')
		->addOption(NO_VALUE, 'all institutions')
		->addOptionsFromTable('nmv__institution', 'ID_institution', "CONCAT(IFNULL(institution_name, '-/-'), '&emsp;---&emsp;', LEFT(IFNULL(location, '-/-'), 50))",
	 		'EXISTS (SELECT * FROM nmv__med_history_tissue WHERE nmv__institution.ID_institution = nmv__med_history_tissue.ID_institution)');


$MPGfilterForm->addField('ID_dataset_origin',SELECT)
	    ->setLabel('MPG Project Data from')
	    ->addOption(NO_VALUE,'all workgroups')
	    ->addOptionsFromTableOrderedById('nmv__dataset_origin', 'ID_dataset_origin', 'work_group');



$MPGfilterForm
	->addButton(BACK)
	->addButton(RESET)
	->addButton(SUBMIT,L_SEARCH);

?>
