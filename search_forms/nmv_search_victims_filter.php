<?php
/**
* creates form for variable search for victims
*
*
*
*/

//query: get experiment-institutions for experiment SELECT
$querystring_experiment = "  SELECT e.ID_experiment AS value, CONCAT(IFNULL(e.experiment_title, 'no entry'), ' &ensp; - &ensp; ID ', e.ID_experiment, ' &ensp; - &ensp; ', IFNULL(i.institution_name, 'no entry')) AS title
                              FROM nmv__experiment e
                              LEFT JOIN nmv__institution i
                              ON e.ID_institution = i.ID_institution
                              ORDER BY title";

// create form
$victimsVariableForm = new Form ('search_victims_variable','nmv_result_victims_variable.php','GET');

// establish database-connection
$victimsVariableForm->addConnection(MYSQL_DB,$db_host,$db_user,$db_pass,$db_name);

// add form-fields
$victimsVariableForm->addField('ID_birth_country', SELECT)
  ->setLabel ('country of birth')
  ->addOption (NO_VALUE,'all countries')
  ->addOptionsFromTable('nmv__country', 'ID_country', 'english');

$victimsVariableForm->addField('birth_year', TEXT, 4)
  ->setLabel ('year of birth (yyyy)');

$victimsVariableForm->addField('twin', CHECKBOX, -1)
  ->setLabel ('twins only');

$victimsVariableForm->addField('br-birth', STATIC_TEXT, '<br>');

$victimsVariableForm->addField('ID_death_country', SELECT)
  ->setLabel ('country of death')
  ->addOption (NO_VALUE,'all countries')
  ->addOptionsFromTable('nmv__country', 'ID_country', 'english');

$victimsVariableForm->addField('death_year', TEXT, 4)
  ->setLabel ('year of death (yyyy)');

$victimsVariableForm->addField('br-death', STATIC_TEXT, '<br>');

$victimsVariableForm->addField('gender', SELECT)
  ->setLabel ('gender')
  ->addOption (NO_VALUE,'all gender')
  ->addOptionsFromArray(['male'=>'male', 'female'=>'female']);

$victimsVariableForm->addField('religion', SELECT)
  ->setLabel ('religion')
  ->addOption (NO_VALUE,'all religions')
  ->addOptionsFromTable('nmv__religion', 'ID_religion', 'english');

$victimsVariableForm->addField('ethnic_group', SELECT)
  ->setLabel ('ethnic group')
  ->addOption (NO_VALUE,'all ethnic groups')
  ->addOptionsFromTable('nmv__ethnicgroup', 'ID_ethnicgroup', 'english');

$victimsVariableForm->addField('nationality_1938', SELECT)
  ->setLabel ('nationality in 1938')
  ->addOption (NO_VALUE,'all nationalities')
  ->addOptionsFromTable('nmv__nationality', 'ID_nationality', 'english');

$victimsVariableForm->addField('ID_education', SELECT)
  ->setLabel ('education')
  ->addOption (NO_VALUE,'all education status')
  ->addOptionsFromTable('nmv__education', 'ID_education', 'english');

$victimsVariableForm->addField('occupation', SELECT)
  ->setLabel ('occupation')
  ->addOption (NO_VALUE,'all occupations')
  ->addOptionsFromTable('nmv__occupation', 'ID_occupation', 'english');

$victimsVariableForm->addField('ns-text', STATIC_TEXT, '<br><br>');

$victimsVariableForm->addField('ID_arrest_country', SELECT)
  ->setLabel ('country of arrest')
  ->addOption (NO_VALUE,'all countries')
  ->addOptionsFromTable('nmv__country', 'ID_country', 'english');

$victimsVariableForm->addField('ID_experiment', SELECT)
  ->setLabel ('experiment')
  ->addOption (NO_VALUE,'all experiments')
  ->addOptionsFromQuery ( "$querystring_experiment");

$victimsVariableForm->addField('ID_classification', SELECT)
  ->setLabel ('imprisonment classification')
  ->addOption (NO_VALUE,'all classifications')
  ->addOptionsFromTable('nmv__victim_classification', 'ID_classification', 'english');

$victimsVariableForm->addField('location', SELECT)
  ->setLabel ('imprisonment location')
  ->addOption (NO_VALUE,'all locations')
  ->addOptionsFromTable('nmv__imprisoniation', 'DISTINCT location', 'location');

$victimsVariableForm->addField('evaluation_status', SELECT)
  ->setLabel ('evaluation status')
  ->addOption (NO_VALUE,'all evaluation status')
  ->addOptionsFromTable('nmv__victim_evaluation_status', 'ID_status', 'english');

  $victimsVariableForm->addField('compensation', SELECT)
    ->setLabel ('compensation')
    ->addOption (NO_VALUE,'all compensations')
    ->addOptionsFromTable('nmv__victim_evaluation_compensation', 'ID_compensation', 'english');

// $victimsVariableForm->addField('confirmation', SELECT)
//   ->setLabel ('evaluation status (outdated?)')
//   ->addOption (NO_VALUE, 'all confirmation status')
//   ->addOptionsFromArray(['confirmed victim', 'not a victim', 'pending']);

$victimsVariableForm->addField('mpg-text', SUBHEADLINE, '<br> &nbsp; MPG-project-related &nbsp; ');

$victimsVariableForm->addField('mpg_project', CHECKBOX, -1)
  ->setLabel ('MPG-project only');

$victimsVariableForm->addField('ID_dataset_origin', SELECT)
  ->setLabel ('workgroup')
  ->addOption (NO_VALUE,'all workgroups')
  ->addOptionsFromTable('nmv__dataset_origin', 'ID_dataset_origin', 'work_group');

$victimsVariableForm->addField('ID_tissue_form', SELECT)
  ->setLabel ('tissue - form')
  ->addOption (NO_VALUE,'all forms')
  ->addOptionsFromTable('nmv__tissue_form', 'ID_tissue_form', 'english');

$victimsVariableForm->addField('ID_tissue_state', SELECT)
  ->setLabel ('tissue - state')
  ->addOption (NO_VALUE,'all states')
  ->addOptionsFromTable('nmv__tissue_state', 'ID_tissue_state', 'english');

$victimsVariableForm->addField('brain_report_year', SELECT)
  ->setLabel ('brain report - year')
  ->addOption (NO_VALUE,'all years')
  ->addOptionsFromRange(1933, 1945);

$victimsVariableForm->addField('brain_report_institution', SELECT)
  ->setLabel ('brain report - institution')
  ->addOption (NO_VALUE,'all institutions')
  ->addValidOptionsFromTable('nmv__institution', 'ID_institution', 'institution_name', 'nmv__med_history_brain');

$victimsVariableForm->addField('brain_report_ID_diagnosis', SELECT)
  ->setLabel ('brain report - diagnosis')
  ->addOption (NO_VALUE,'all diagnoses')
  ->addOptionsFromTable('nmv__diagnosis', 'ID_diagnosis', "CONCAT(IFNULL(english, 'no entry'),' - ',IFNULL(type, 'no entry'))");

$victimsVariableForm->addField('brain_report_diagnosis', TEXT, 120)
->setLabel ('brain report - diagnosis (not standardized yet)');

$victimsVariableForm->addField('hospitalisation_year', TEXT, 4)
  ->setLabel ('hospitalisation - entry year (yyyy)');

  $victimsVariableForm->addField('hospitalisation_institution', SELECT)
    ->setLabel ('hospitalisation - institution')
    ->addOption (NO_VALUE,'all institutions')
    ->addValidOptionsFromTable('nmv__institution', 'ID_institution', 'institution_name', 'nmv__med_history_hosp');

$victimsVariableForm->addField('hospitalisation_ID_diagnosis', SELECT)
  ->setLabel ('hospitalisation - diagnosis')
  ->addOption (NO_VALUE,'all diagnoses')
  ->addOptionsFromTable('nmv__diagnosis', 'ID_diagnosis', "CONCAT(IFNULL(english, 'no entry'),' - ',IFNULL(type, 'no entry'))");

$victimsVariableForm->addField('hospitalisation_diagnosis', TEXT, 120)
->setLabel ('hospitalisation - diagnosis (not standardized yet)');

$victimsVariableForm->addField('post_1945-text', SUBHEADLINE, '<br> &nbsp; Survivors: after 1945 &nbsp; ');

$victimsVariableForm->addField ('residence_after_1945_country',TEXT,120)
	->setLabel ('after 1945 - residence (country)');

$victimsVariableForm->addField ('occupation_after_1945',TEXT,120)
	->setLabel ('after 1945 - occupation');

$victimsVariableForm->addField('nationality_after_1945', SELECT)
  ->setLabel ('after 1945 - nationality')
  ->addOption (NO_VALUE,'all nationalities')
  ->addOptionsFromTable('nmv__nationality', 'ID_nationality', 'english');



// add buttons
$victimsVariableForm
	->addButton (BACK)
	->addButton (RESET)
	->addButton (SUBMIT,L_SEARCH);
?>
