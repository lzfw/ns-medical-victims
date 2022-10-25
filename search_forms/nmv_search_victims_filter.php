<?php
/**
* creates form for variable search for victims
*
*
*
*/

//query: get experiment-institutions for experiment SELECT
$querystring_experiment = "  SELECT e.ID_experiment AS value, CONCAT(IFNULL(e.experiment_title, 'no entry'), ' &ensp; - &ensp; ID ', e.ID_experiment) AS title
                              FROM nmv__experiment e
                              ORDER BY title";

// create form
$victimsVariableForm = new Form ('search_victims_variable','nmv_result_victims_variable.php','GET');

// establish database-connection
$victimsVariableForm->addConnection(MYSQL_DB,$db_host,$db_user,$db_pass,$db_name);

// add form-fields
$victimsVariableForm->addField('ID_birth_country', SELECT)
  ->setLabel('country of birth')
  ->addOption(NO_VALUE,'all countries')
  ->addOptionsFromTable('nmv__country', 'ID_country', 'english',
                        'EXISTS (	SELECT *
                                  FROM nmv__victim
                                  WHERE nmv__country.ID_country = nmv__victim.ID_birth_country)');

$victimsVariableForm->addField('birth_place', TEXT, 120)
                                    ->setLabel('place of birth <small>contains</small>');

$victimsVariableForm->addField('birth_year', TEXT, 4)
  ->setLabel('year of birth (yyyy)');

$victimsVariableForm->addField('twin', CHECKBOX, -1)
  ->setLabel('twins only');

$victimsVariableForm->addField('br-birth', STATIC_TEXT, '<br>');

$victimsVariableForm->addField('ID_death_institution', SELECT)
  ->setLabel('institution of death')
  ->addOption(NO_VALUE,'all instiutions')
  ->addOptionsFromTable('nmv__institution', 'ID_institution', 'institution_name',
                        'EXISTS (	SELECT *
                                  FROM nmv__victim
                                  WHERE nmv__institution.ID_institution = nmv__victim.ID_death_institution)');

$victimsVariableForm->addField('ID_death_country', SELECT)
  ->setLabel('country of death')
  ->addOption(NO_VALUE,'all countries')
  ->addOptionsFromTable('nmv__country', 'ID_country', 'english',
                        'EXISTS (	SELECT *
                                  FROM nmv__victim
                                  WHERE nmv__country.ID_country = nmv__victim.ID_death_country)');

$victimsVariableForm->addField('death_place', TEXT, 120)
  ->setLabel('place of death <small>contains</small>');

$victimsVariableForm->addField('death_year', TEXT, 4)
  ->setLabel('year of death (yyyy)');

$victimsVariableForm->addField('br-death', STATIC_TEXT, '<br>');

$victimsVariableForm->addField('gender', SELECT)
  ->setLabel('gender')
  ->addOption(NO_VALUE,'all gender')
  ->addOptionsFromArray(['male'=>'male', 'female'=>'female']);

$victimsVariableForm->addField('religion', SELECT)
  ->setLabel('religion')
  ->addOption(NO_VALUE,'all religions')
  ->addOptionsFromTable('nmv__religion', 'ID_religion', 'english',
                        'EXISTS (	SELECT *
                                  FROM nmv__victim
                                  WHERE nmv__religion.ID_religion = nmv__victim.religion)');

$victimsVariableForm->addField('ethnic_group', SELECT)
  ->setLabel('ethnic group')
  ->addOption(NO_VALUE,'all ethnic groups')
  ->addOptionsFromTable('nmv__ethnicgroup', 'ID_ethnicgroup', 'english',
                        'EXISTS (	SELECT *
                                  FROM nmv__victim
                                  WHERE nmv__ethnicgroup.ID_ethnicgroup = nmv__victim.ethnic_group)');

$victimsVariableForm->addField('nationality_1938', SELECT)
  ->setLabel('nationality in 1938')
  ->addOption(NO_VALUE,'all nationalities')
  ->addOptionsFromTable('nmv__nationality', 'ID_nationality', 'english',
                        'EXISTS (	SELECT *
                                  FROM nmv__victim
                                  WHERE nmv__nationality.ID_nationality = nmv__victim.nationality_1938)');

$victimsVariableForm->addField('ID_education', SELECT)
  ->setLabel('education')
  ->addOption(NO_VALUE,'all education status')
  ->addOptionsFromTable('nmv__education', 'ID_education', 'english',
                        'EXISTS (	SELECT *
                                  FROM nmv__victim
                                  WHERE nmv__education.ID_education = nmv__victim.ID_education)');

$victimsVariableForm->addField('occupation', SELECT)
  ->setLabel('occupation')
  ->addOption(NO_VALUE,'all occupations')
  ->addOptionsFromTable('nmv__occupation', 'ID_occupation', 'english',
                        'EXISTS (	SELECT *
                                  FROM nmv__victim
                                  WHERE nmv__occupation.ID_occupation = nmv__victim.occupation)');

$victimsVariableForm->addField('ns-text', STATIC_TEXT, '<br><br>');

$victimsVariableForm->addField('ID_arrest_country', SELECT)
  ->setLabel('country of arrest')
  ->addOption(NO_VALUE,'all countries')
  ->addOptionsFromTable('nmv__country', 'ID_country', 'english',
                        'EXISTS (	SELECT *
                                  FROM nmv__victim
                                  WHERE nmv__country.ID_country = nmv__victim.ID_arrest_country)');

// complete db d 1
if (!($dbi->checkUserPermission('mpg'))) :
  $victimsVariableForm->addField('ID_experiment', SELECT)
    ->setLabel('experiment')
    ->addOption(NO_VALUE,'all experiments')
    ->addOptionsFromQuery( "$querystring_experiment");

  $victimsVariableForm->addField('exp_institution', SELECT)
      ->setLabel('experiment - institution')
      ->addOption(NO_VALUE, 'all experiment-institutions')
      ->addOptionsFromTable('nmv__institution', 'ID_institution', "CONCAT(IFNULL(institution_name, '-/-'), '&emsp;---&emsp;', LEFT(IFNULL(location, '-/-'), 50))",
                            'EXISTS (SELECT *
                                      FROM nmv__experiment_institution
                                      WHERE nmv__experiment_institution.ID_institution = nmv__institution.ID_institution)');

  $victimsVariableForm->addField('ID_foi', SELECT)
      ->setLabel('experiment - fields of interest')
      ->addOption(NO_VALUE, 'all fields of interest')
      ->addOptionsFromTable('nmv__field_of_interest', 'ID_foi', 'english');

endif;

$victimsVariableForm->addField('ID_classification', SELECT)
  ->setLabel('imprisonment classification')
  ->addOption(NO_VALUE,'all classifications')
  ->addOptionsFromTable('nmv__victim_classification', 'ID_classification', 'english');

$victimsVariableForm->addField('ID_imprisonment_institution', SELECT)
  ->setLabel('imprisonment institution')
  ->addOption(NO_VALUE,'all institutions')
  ->addOptionsFromTable('nmv__institution', 'ID_institution', 'institution_name',
                        'EXISTS (	SELECT *
                                  FROM nmv__imprisoniation
                                  WHERE nmv__institution.ID_institution = nmv__imprisoniation.ID_institution)');

$victimsVariableForm->addField('location', SELECT)
  ->setLabel('imprisonment location')
  ->addOption(NO_VALUE,'all locations')
  ->addOptionsFromTable('nmv__imprisoniation', 'DISTINCT location', 'location');

$victimsVariableForm->addField('evaluation_status', SELECT)
  ->setLabel('evaluation status')
  ->addOption(NO_VALUE,'all evaluation status')
  ->addOptionsFromTable('nmv__victim_evaluation_status', 'ID_status', 'english');

$victimsVariableForm->addField('mpg-text', SUBHEADLINE, '<br> &nbsp; MPG-project-related &nbsp; ');

// complete db d 2
if (!($dbi->checkUserPermission('mpg'))) :
  $victimsVariableForm->addField('mpg_project', CHECKBOX, -1)
    ->setLabel('MPG-project only');
endif;

$victimsVariableForm->addField('ID_dataset_origin', SELECT)
  ->setLabel('workgroup')
  ->addOption(NO_VALUE,'all workgroups')
  ->addOptionsFromTable('nmv__dataset_origin', 'ID_dataset_origin', 'work_group');

$victimsVariableForm->addField('br-tissue', STATIC_TEXT, '<br>');

$victimsVariableForm->addField('tissue_institution', SELECT)
    ->setLabel('tissue - institution')
    ->addOption(NO_VALUE, 'all institutions')
    ->addValidOptionsFromTable('nmv__institution', 'ID_institution', "CONCAT(IFNULL(institution_name, '-/-'), '&emsp;---&emsp;', LEFT(IFNULL(location, '-/-'), 50))", 'nmv__med_history_tissue');

$victimsVariableForm->addField('ID_tissue_form', SELECT)
  ->setLabel('tissue - form')
  ->addOption(NO_VALUE,'all forms')
  ->addOptionsFromTable('nmv__tissue_form', 'ID_tissue_form', 'english');

$victimsVariableForm->addField('ID_tissue_state', SELECT)
  ->setLabel('tissue - state')
  ->addOption(NO_VALUE,'all states')
  ->addOptionsFromTable('nmv__tissue_state', 'ID_tissue_state', 'english');

$victimsVariableForm->addField('ref_no_tissue', TEXT, 120)
  ->setLabel('tissue - Reference Number');

$victimsVariableForm->addField('br-brain', STATIC_TEXT, '<br>');

$victimsVariableForm->addField('brain_report_year', SELECT)
  ->setLabel('brain report - year')
  ->addOption(NO_VALUE,'all years')
  ->addOptionsFromRange(1933, 1945);

$victimsVariableForm->addField('brain_report_institution', SELECT)
  ->setLabel('brain report - institution')
  ->addOption(NO_VALUE,'all institutions')
  ->addValidOptionsFromTable('nmv__institution', 'ID_institution', "CONCAT(IFNULL(institution_name, '-/-'), '&emsp;---&emsp;', LEFT(IFNULL(location, '-/-'), 50))", 'nmv__med_history_brain');

$victimsVariableForm->addField('ref_no_brain', TEXT, 120)
  ->setLabel('brain report - Reference Number');

$victimsVariableForm->addField('brain_report_ID_diagnosis', SELECT)
  ->setLabel('brain report - diagnosis tags')
  ->addOption(NO_VALUE,'all diagnoses')
  ->addOptionsFromTable('nmv__diagnosis_tag', 'ID_diagnosis', 'diagnosis',
                        'EXISTS (	SELECT *
                                  FROM nmv__diagnosis_brain
                                  WHERE nmv__diagnosis_brain.ID_diagnosis = nmv__diagnosis_tag.ID_diagnosis)');

$victimsVariableForm->addField('brain_report_diagnosis', TEXT, 120)
  ->setLabel('brain report - diagnosis <br><small>(search for keyword in freetext and in tags)</small>');

$victimsVariableForm->addField('br-hosp', STATIC_TEXT, '<br>');

$victimsVariableForm->addField('hospitalisation_year', TEXT, 4)
  ->setLabel('hospitalisation - entry year (yyyy)');

  $victimsVariableForm->addField('hospitalisation_institution', SELECT)
    ->setLabel('hospitalisation - institution')
    ->addOption(NO_VALUE,'all institutions')
    ->addValidOptionsFromTable('nmv__institution', 'ID_institution', "CONCAT(IFNULL(institution_name, '-/-'), '&emsp;---&emsp;', LEFT(IFNULL(location, '-/-'), 50))", 'nmv__med_history_hosp');

$victimsVariableForm->addField('autopsy_ref_no', TEXT, 120)
    ->setLabel('hospitalisation - Autopsy Number');

$victimsVariableForm->addField('hospitalisation_ID_diagnosis', SELECT)
  ->setLabel('hospitalisation - diagnosis tags')
  ->addOption(NO_VALUE,'all diagnoses')
  ->addOptionsFromTable('nmv__diagnosis_tag', 'ID_diagnosis', 'diagnosis',
                        'EXISTS (	SELECT *
                                  FROM nmv__diagnosis_hosp
                                  WHERE nmv__diagnosis_hosp.ID_diagnosis = nmv__diagnosis_tag.ID_diagnosis)');

$victimsVariableForm->addField('hospitalisation_diagnosis', TEXT, 120)
->setLabel('hospitalisation - diagnosis <br><small>(search for keyword in freetext and in tags)</small><br>');


// complete db d 3
if (!($dbi->checkUserPermission('mpg'))) :
  $victimsVariableForm->addField('post_1945-text', SUBHEADLINE, '<br> &nbsp; Survivors: after 1945 &nbsp; ');

  $victimsVariableForm->addField('residence_after_1945_country', TEXT, 120)
  	->setLabel('after 1945 - residence (country)');

  $victimsVariableForm->addField('occupation_after_1945', TEXT, 120)
  	->setLabel('after 1945 - occupation');

  $victimsVariableForm->addField('nationality_after_1945', SELECT)
    ->setLabel('after 1945 - nationality')
    ->addOption(NO_VALUE,'all nationalities')
    ->addOptionsFromTable('nmv__nationality', 'ID_nationality', 'english',
                          'EXISTS (SELECT *
                                    FROM nmv__victim
                                    WHERE nmv__nationality.ID_nationality = nmv__victim.nationality_after_1945)');
endif;

$victimsVariableForm->addField('notes-text', SUBHEADLINE, '<br> &nbsp; keyword search in notes &nbsp;');
$victimsVariableForm->addField('notes', TEXT, 120)
  ->setLabel('keyword in "notes"');
$victimsVariableForm->addField('notes_after_1945', TEXT, 120)
  ->setLabel('keyword in "notes after 1945"');
$victimsVariableForm->addField('notes_photo', TEXT, 120)
  ->setLabel('keyword in "notes about photo"');

$victimsVariableForm->addField('photo-text', STATIC_TEXT, '<br> &nbsp;  &nbsp;');
$victimsVariableForm->addField('photo', CHECKBOX, -1)
  ->setLabel('photo contained <br> <small>in source, literature, medical record or brain report</small>');





// add buttons
$victimsVariableForm
	->addButton(BACK)
	->addButton(RESET)
	->addButton(SUBMIT,L_SEARCH);
?>
