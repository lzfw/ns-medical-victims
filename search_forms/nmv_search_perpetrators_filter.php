<?php
/**
* creates form for variable search (filter) for perpetrators
*
*
*
*/

// create form
$perpetratorsFilterForm = new Form ('search_perpetrators_filter','nmv_result_perpetrators_filter.php','GET');

// establish database-connection
$perpetratorsFilterForm->addConnection(MYSQL_DB,$db_host,$db_user,$db_pass,$db_name);

// add form-fields
$perpetratorsFilterForm->addField('ID_birth_country', SELECT)
  ->setLabel ('Country of Birth')
  ->addOption (NO_VALUE,'all countries')
  ->addOptionsFromTable('nmv__country', 'ID_country', 'english',
                        'EXISTS (	SELECT *
                                  FROM nmv__perpetrator
                                  WHERE nmv__country.ID_country = nmv__perpetrator.ID_birth_country)');

$perpetratorsFilterForm->addField('birth_year', TEXT, 4)
  ->setLabel ('Year of Birth (yyyy)');

$perpetratorsFilterForm->addField('br-birth', STATIC_TEXT, '<br>');

$perpetratorsFilterForm->addField('ID_death_country', SELECT)
  ->setLabel ('Country of Death')
  ->addOption (NO_VALUE,'all countries')
  ->addOptionsFromTable('nmv__country', 'ID_country', 'english',
                        'EXISTS (	SELECT *
                                  FROM nmv__perpetrator
                                  WHERE nmv__country.ID_country = nmv__perpetrator.ID_death_country)');

$perpetratorsFilterForm->addField('death_year', TEXT, 4)
  ->setLabel ('Year of Death (yyyy)');

$perpetratorsFilterForm->addField('br-death', STATIC_TEXT, '<br>');

$perpetratorsFilterForm->addField('gender', SELECT)
  ->setLabel ('Gender')
  ->addOption (NO_VALUE, 'all gender')
  ->addOptionsFromArray(['male' => 'male', 'female' => 'female']);

$perpetratorsFilterForm->addField('religion', SELECT)
  ->setLabel ('Religion')
  ->addOption (NO_VALUE,'all religions')
//  ->addOption ('NULL', 'no entry')
  ->addOptionsFromTable('nmv__religion', 'ID_religion', 'english', 'EXISTS (SELECT * FROM nmv__perpetrator
              WHERE nmv__religion.ID_religion = nmv__perpetrator.religion)');

$perpetratorsFilterForm->addField('nationality_1938', SELECT)
  ->setLabel ('Nationality in 1938')
  ->addOption (NO_VALUE,'all nationalities')
  ->addOptionsFromTable('nmv__nationality', 'ID_nationality', 'english', 'EXISTS (SELECT * FROM nmv__perpetrator
              WHERE nmv__nationality.ID_nationality = nmv__perpetrator.nationality_1938)');

$perpetratorsFilterForm->addField('br-career-education', STATIC_TEXT, '<br>');

$perpetratorsFilterForm->addField('titles', TEXT, 100)
  ->setLabel ('Titles (keyword)');

$perpetratorsFilterForm->addField('place_of_qualification', TEXT, 100)
 ->setLabel ('Place of qualification (keyword)');

$perpetratorsFilterForm->addField('year_of_qualification', TEXT, 4)
 ->setLabel ('Year of qualification (yyyy)');

$perpetratorsFilterForm->addField('title_of_dissertation', TEXTAREA, 2)
  ->setLabel ('Title of dissertation (keyword)');

$perpetratorsFilterForm->addField('occupation', TEXT, 100)
  ->setLabel ('Occupation (keyword)');

$perpetratorsFilterForm->addField('ID_perp_class', SELECT)
  ->setLabel ('Classification')
  ->addOption (NO_VALUE, 'all classifications')
  ->addOptionsFromTable('nmv__perpetrator_classification', 'ID_perp_class', 'english');

$perpetratorsFilterForm->addField('career_history', TEXT, 100)
  ->setLabel ('Career history (keyword)');

$perpetratorsFilterForm->addField('br-memberships', STATIC_TEXT, '<br>');

$perpetratorsFilterForm->addField('nsdap_member', CHECKBOX, -1)
  ->setLabel ('NSDAP member');

$perpetratorsFilterForm->addField('ss_member', CHECKBOX, -1)
  ->setLabel ('SS member');

$perpetratorsFilterForm->addField('sa_member', CHECKBOX, -1)
  ->setLabel ('SA member');

$perpetratorsFilterForm->addField('other_nsdap_organisations_member', CHECKBOX, -1)
  ->setLabel ('Other NSDAP Organisations');

$perpetratorsFilterForm->addField('details_all_memberships', TEXT, 100)
  ->setLabel ('Details all memberships (keyword)');

$perpetratorsFilterForm->addField('br-after 1945', STATIC_TEXT, '<br>');

$perpetratorsFilterForm->addField('prosecution', TEXT, 50)
  ->setLabel ('Prosecution (keyword)');

$perpetratorsFilterForm->addField('prison_time', TEXT, 50)
  ->setLabel ('Prison time (keyword)');

$perpetratorsFilterForm->addField('career_after_1945', TEXT, 100)
  ->setLabel ('Career after 1945 (keyword)');

$perpetratorsFilterForm->addField('notes', TEXT, 100)
  ->setLabel('Notes (keyword)');





// add buttons
$perpetratorsFilterForm
	->addButton (BACK)
	->addButton (RESET)
	->addButton (SUBMIT,L_SEARCH);

?>
