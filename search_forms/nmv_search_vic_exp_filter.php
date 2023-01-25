<?php
/**
* creates form for variable search for victims
*
*
*
*/

// create form
$victimExperimentForm = new Form ('search_vic_exp','nmv_result_vic_exp_filter.php','GET');

// establish database-connection
$victimExperimentForm->addConnection(MYSQL_DB,$db_host,$db_user,$db_pass,$db_name);

// add form-fields
$victimExperimentForm->addField('ID_birth_country', SELECT)
  ->setLabel ('country of birth')
  ->addOption (NO_VALUE,'all countries')
  ->addOptionsFromTable('nmv__country', 'ID_country', 'english',
                        'EXISTS (	SELECT *
                                  FROM nmv__victim
                                  WHERE nmv__country.ID_country = nmv__victim.ID_birth_country)');


$victimExperimentForm->addField('ID_nationality_1938', SELECT)
  ->setLabel ('nationality in 1938')
  ->addOption (NO_VALUE,'all nationalities')
  ->addOptionsFromTable('nmv__nationality', 'ID_nationality', 'english',
                        'EXISTS (	SELECT *
                                  FROM nmv__victim
                                  WHERE nmv__nationality.ID_nationality = nmv__victim.ID_nationality_1938)');

// add buttons
$victimExperimentForm
	->addButton (BACK)
	->addButton (RESET)
	->addButton (SUBMIT,L_SEARCH);
?>
