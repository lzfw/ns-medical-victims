<?php
/**
* creates form for filter victims in mpg-project
*
*
*
*/


// victimgroups from MPG project search form
$MPGfilterForm = new Form ('search_mpg_group','nmv_result_mpg_filter.php','GET');

$MPGfilterForm->addConnection(MYSQL_DB,$db_host,$db_user,$db_pass,$db_name);

$MPGfilterForm->addField ('cause_of_death', CHECKBOX, -1)
	->setLabel ('Cause of Death: executed');

$MPGfilterForm->addField ('prisoner_of_war', CHECKBOX, -1)
	->setLabel ('Imprisonment: Prisoner of War');

$MPGfilterForm->addField ('psychiatric_patient', CHECKBOX, -1)
	->setLabel ('Imprisonment: Psychiatric Patient');

$MPGfilterForm->addField ('beddies_database', CHECKBOX, -1)
	->setLabel ('Beddies Database');

$MPGfilterForm->addField ('m_series', CHECKBOX, -1)
	->setLabel ('M-Series');


$MPGfilterForm->addField ('ID_institution',SELECT)
	    ->setLabel ('Institution')
	    ->addOption (NO_VALUE,'all institutions')
	    ->addOptionsFromTable( 'nmv__institution', 'ID_institution', 'institution_name',
				'EXISTS (	SELECT * FROM nmv__med_history_brain
									WHERE nmv__institution.ID_institution = nmv__med_history_brain.ID_institution)
				OR EXISTS (SELECT * FROM nmv__med_history_hosp
				WHERE nmv__institution.ID_institution = nmv__med_history_hosp.ID_institution)');

// $MPGgroupForm->addField ('ID_institution',SELECT)
// 	    ->setLabel ('Institution')
// 	    ->addOption (NO_VALUE,'all institutions')
// 	    ->addOptionsFromTableOrderedById ( 'nmv__institution', 'ID_institution', 'institution_name', 'ID_institution IN (39, 51, 54, 56, 67, 68, 70, 84, 94, 97, 105, 106, 113, 114, 115, 117, 118, 119, 120, 122, 123, 124, 125, 126, 127)');

$MPGfilterForm->addField ('ID_dataset_origin',SELECT)
	    ->setLabel ('MPG Project Data from')
	    ->addOption (NO_VALUE,'all workgroups')
	    ->addOptionsFromTableOrderedById ( 'nmv__dataset_origin', 'ID_dataset_origin', 'work_group');



$MPGfilterForm
	->addButton (BACK)
	->addButton (RESET)
	->addButton (SUBMIT,L_SEARCH);

?>
