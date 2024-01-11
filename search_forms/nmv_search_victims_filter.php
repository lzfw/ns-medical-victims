<?php
/**
* creates form for variable search for victims
*
*
*
*/


//query: get experiment-institutions for experiment SELECT
$querystring_experiment = "  SELECT e.ID_experiment AS value,
                                    CONCAT(IFNULL(e.experiment_title, 'no entry'), ' &ensp; - &ensp; ID ',
                                                  e.ID_experiment, ' &ensp; - &ensp; ',
                                                  IFNULL(i.institution_name, 'no entry')) AS title
                              FROM nmv__experiment e
                              LEFT JOIN nmv__institution i ON e.ID_institution = i.ID_institution
                              ORDER BY title";


// create form
$victimsVariableForm = new Form ('search_victims_variable','nmv_result_victims_variable.php','GET');

// establish database-connection
$victimsVariableForm->addConnection(MYSQL_DB,$db_host,$db_user,$db_pass,$db_name);

// add form-fields
$victimsVariableForm->addField('ID_birth_country', SELECT)
  ->setLabel('country of birth')
  ->addOption(NO_VALUE,'all countries')
  ->addOption('NULL')
  ->addOptionsFromTable('nmv__country', 'ID_country', 'country',
                        'EXISTS (	SELECT *
                                  FROM nmv__victim
                                  WHERE nmv__country.ID_country = nmv__victim.ID_birth_country)');

$victimsVariableForm->addField('birth_place', TEXT, 120)
                                    ->setLabel('place of birth <small>contains keyword</small>');

$victimsVariableForm->addField('birth_year', TEXT, 4)
  ->setLabel('year of birth (yyyy)');

$victimsVariableForm->addField('twin', CHECKBOX, -1)
  ->setLabel('twins only');

$victimsVariableForm->addField('br-birth', STATIC_TEXT, '<br>');

$victimsVariableForm->addField('ID_death_institution', SELECT)
  ->setLabel('institution of death')
  ->addOption(NO_VALUE,'all institutions')
  ->addOption('NULL')
  ->addOptionsFromTable('nmv__institution', 'ID_institution', 'institution_name',
                        'EXISTS (	SELECT *
                                  FROM nmv__victim
                                  WHERE nmv__institution.ID_institution = nmv__victim.ID_death_institution)');

$victimsVariableForm->addField('ID_death_country', SELECT)
  ->setLabel('country of death')
  ->addOption(NO_VALUE,'all countries')
  ->addOption('NULL')
  ->addOptionsFromTable('nmv__country', 'ID_country', 'country',
                        'EXISTS (	SELECT *
                                  FROM nmv__victim
                                  WHERE nmv__country.ID_country = nmv__victim.ID_death_country)');

$victimsVariableForm->addField('death_place', TEXT, 120)
  ->setLabel('place of death <small>contains keyword</small>');

$victimsVariableForm->addField('death_year', TEXT, 4)
  ->setLabel('year of death (yyyy)');

$victimsVariableForm->addField('br-death', STATIC_TEXT, '<br>');

$victimsVariableForm->addField('gender', SELECT)
  ->setLabel('gender')
  ->addOption(NO_VALUE,'all gender')
  ->addOption('NULL')
  ->addOptionsFromArray(['male'=>'male', 'female'=>'female']);

$victimsVariableForm->addField('ID_religion', SELECT)
  ->setLabel('religion')
  ->addOption(NO_VALUE,'all religions')
  ->addOption('NULL')
  ->addOptionsFromTable('nmv__religion', 'ID_religion', 'religion',
                        'EXISTS (	SELECT *
                                  FROM nmv__victim
                                  WHERE nmv__religion.ID_religion = nmv__victim.ID_religion)');

$victimsVariableForm->addField('ID_ethnic_group', SELECT)
  ->setLabel('ascribed ethnic group')
  ->addOption(NO_VALUE,'all ethnic groups')
  ->addOption('NULL')
  ->addOptionsFromTable('nmv__ethnic_group', 'ID_ethnic_group', 'ethnic_group',
                        'EXISTS (	SELECT *
                                  FROM nmv__victim
                                  WHERE nmv__ethnic_group.ID_ethnic_group = nmv__victim.ID_ethnic_group)');

$victimsVariableForm->addField('ID_nationality_1938', SELECT)
  ->setLabel('nationality in 1938')
  ->addOption(NO_VALUE,'all nationalities')
  ->addOption('NULL')
  ->addOptionsFromTable('nmv__nationality', 'ID_nationality', 'nationality',
                        'EXISTS (	SELECT *
                                  FROM nmv__victim
                                  WHERE nmv__nationality.ID_nationality = nmv__victim.ID_nationality_1938)');

$victimsVariableForm->addField('ID_education', SELECT)
  ->setLabel('education')
  ->addOption(NO_VALUE,'all education status')
  ->addOption('NULL')
  ->addOptionsFromTable('nmv__education', 'ID_education', 'education',
                        'EXISTS (	SELECT *
                                  FROM nmv__victim
                                  WHERE nmv__education.ID_education = nmv__victim.ID_education)');

$victimsVariableForm->addField('ID_occupation', SELECT)
  ->setLabel('occupation')
  ->addOption(NO_VALUE,'all occupations')
  ->addOption('NULL')
  ->addOptionsFromTable('nmv__occupation', 'ID_occupation', 'occupation',
                        'EXISTS (	SELECT *
                                  FROM nmv__victim
                                  WHERE nmv__occupation.ID_occupation = nmv__victim.ID_occupation)');

$victimsVariableForm->addField('ns-text', STATIC_TEXT, '<br><br>');

$victimsVariableForm->addField('ID_arrest_country', SELECT)
  ->setLabel('country of arrest')
  ->addOption(NO_VALUE,'all countries')
  ->addOption('NULL')
  ->addOptionsFromTable('nmv__country', 'ID_country', 'country',
                        'EXISTS (	SELECT *
                                  FROM nmv__victim
                                  WHERE nmv__country.ID_country = nmv__victim.ID_arrest_country)');

// complete db d 1
if (!($dbi->checkUserPermission('mpg'))) :
  $victimsVariableForm->addField('ID_experiment', SELECT)
    ->setLabel('experiment')
    ->addOption(NO_VALUE,'all experiments')
    ->addOption('NULL', 'NO EXPERIMENT LINKED')
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
      ->addOptionsFromTable('nmv__field_of_interest', 'ID_foi', 'field_of_interest');

endif;

$victimsVariableForm->addField('ID_classification', SELECT)
  ->setLabel('imprisonment classification')
  ->addOption(NO_VALUE,'all classifications')
  ->addOption('NULL', 'imprisonments without classification entry')
  ->addOptionsFromTable('nmv__victim_classification', 'ID_classification', 'classification');

$victimsVariableForm->addField('ID_imprisonment_institution', SELECT)
  ->setLabel('imprisonment institution')
  ->addOption(NO_VALUE,'all institutions')
  ->addOptionsFromTable('nmv__institution', 'ID_institution', 'institution_name',
                        'EXISTS (	SELECT *
                                  FROM nmv__imprisonment
                                  WHERE nmv__institution.ID_institution = nmv__imprisonment.ID_institution)');

$victimsVariableForm->addField('location', SELECT)
  ->setLabel('imprisonment location')
  ->addOption(NO_VALUE,'all locations')
  ->addOptionsFromTable('nmv__imprisonment', 'DISTINCT location', 'location');

$victimsVariableForm->addField('arrest_history', TEXT, 120)
  ->setLabel('arrest history <small>contains keyword</small>');

$victimsVariableForm->addField('compensation', SELECT)
    ->setLabel('compensation')
    ->addOption(NO_VALUE, 'all compensation options')
    ->addOptionsFromArray(array('yes'=>'yes', 'no'=>'no', 'not applicable'=>'not applicable', 'not specified'=>'not specified'));

$victimsVariableForm->addField('ID_evaluation_status', SELECT)
  ->setLabel('evaluation status')
  ->addOption(NO_VALUE,'all evaluation status')
  ->addOption('NULL')
  ->addOptionsFromTable('nmv__victim_evaluation_status', 'ID_evaluation_status', 'status');

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

$victimsVariableForm->addField('entry_status', SELECT)
    ->setLabel('status data transfer (temporary)')
    ->addOption(NO_VALUE, 'all statuses')
    ->addOptionsFromArray(["Data entry (Halle)"=>"Data entry (Halle)", "Review (Vienna/Berlin/Munich)"=>"Review (Vienna/Berlin/Munich)",
                            "Revision (Halle)"=>"Revision (Halle)", "Validated"=>"Validated"]);

$victimsVariableForm->addField('potential_old_profile', CHECKBOX, -1)
    ->setLabel('Potential pre mpg-project profile (temporary)');


$victimsVariableForm->addField('br-tissue', STATIC_TEXT, '<br>');

$victimsVariableForm->addField('tissue_institution', SELECT)
    ->setLabel('tissue - institution')
    ->addOption(NO_VALUE, 'all institutions')
    ->addValidOptionsFromTable('nmv__institution', 'ID_institution', "CONCAT(IFNULL(institution_name, '-/-'), '&emsp;---&emsp;', LEFT(IFNULL(location, '-/-'), 50))", 'nmv__med_history_tissue');

$victimsVariableForm->addField('ID_tissue_form', SELECT)
  ->setLabel('tissue - form')
  ->addOption(NO_VALUE,'all forms')
  ->addOptionsFromTable('nmv__tissue_form', 'ID_tissue_form', 'tissue_form');

$victimsVariableForm->addField('ID_tissue_state', SELECT)
  ->setLabel('tissue - state')
  ->addOption(NO_VALUE,'all states')
  ->addOptionsFromTable('nmv__tissue_state', 'ID_tissue_state', 'tissue_state');

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

$victimsVariableForm->addField('clinical_ID_diagnosis', SELECT)
  ->setLabel('clinical - diagnosis tags')
  ->addOption(NO_VALUE,'all diagnoses')
  ->addOptionsFromTable('nmv__diagnosis_tag', 'ID_diagnosis', 'diagnosis',
                        'EXISTS (	SELECT *
                                  FROM nmv__diagnosis_hosp
                                  WHERE nmv__diagnosis_hosp.ID_diagnosis = nmv__diagnosis_tag.ID_diagnosis)
                         OR EXISTS (	SELECT *
                                  FROM nmv__diagnosis_diagnosis
                                  WHERE nmv__diagnosis_diagnosis.ID_diagnosis = nmv__diagnosis_tag.ID_diagnosis)'
  );

$victimsVariableForm->addField('clinical_diagnosis', TEXT, 120)
->setLabel('clinical - diagnosis <br><small>(search for keyword in freetext and in tags)</small><br>');


// complete db d 3
if (!($dbi->checkUserPermission('mpg'))) :
  $victimsVariableForm->addField('post_1945-text', SUBHEADLINE, '<br> &nbsp; Survivors: after 1945 &nbsp; ');

  $victimsVariableForm->addField('residence_after_1945_country', TEXT, 120)
  	->setLabel('after 1945 - residence (country)');

  $victimsVariableForm->addField('occupation_after_1945', TEXT, 120)
  	->setLabel('after 1945 - occupation');

  $victimsVariableForm->addField('ID_nationality_after_1945', SELECT)
    ->setLabel('after 1945 - nationality')
    ->addOption(NO_VALUE,'all nationalities')
    ->addOptionsFromTable('nmv__nationality', 'ID_nationality', 'nationality',
                          'EXISTS (SELECT *
                                    FROM nmv__victim
                                    WHERE nmv__nationality.ID_nationality = nmv__victim.ID_nationality_after_1945)');
endif;

$victimsVariableForm->addField('notes-text', SUBHEADLINE, '<br> &nbsp; keyword search in notes &nbsp;');
$victimsVariableForm->addField('notes', TEXT, 120)
  ->setLabel('keyword in "notes"');
$victimsVariableForm->addField('internal_notes', TEXT, 120)
  ->setLabel('keyword in "internal notes"');
$victimsVariableForm->addField('notes_after_1945', TEXT, 120)
  ->setLabel('keyword in "notes after 1945"');
$victimsVariableForm->addField('notes_photo', TEXT, 120)
  ->setLabel('keyword in "notes about photo"');

$victimsVariableForm->addField('photo-text', STATIC_TEXT, '<br> &nbsp;  &nbsp;');
$victimsVariableForm->addField('photo', CHECKBOX, -1)
  ->setLabel('photo contained <br> <small>in source, literature, medical record or brain report</small>');
$victimsVariableForm->addField('stolperstein_exists', CHECKBOX, -1)
    ->setLabel('stolperstein is known');





// add buttons
$victimsVariableForm
	->addButton(BACK)
	->addButton(RESET)
	->addButton(SUBMIT,L_SEARCH);
?>
