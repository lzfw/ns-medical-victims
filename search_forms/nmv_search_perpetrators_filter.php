<?php
/**
* creates form for variable search (filter) for perpetrators
*
*
*
*/

// create form
$perpetratorsFilterForm = new Form('search_perpetrators_filter','nmv_result_perpetrators_filter.php','GET');

// establish database-connection
$perpetratorsFilterForm->addConnection(MYSQL_DB,$db_host,$db_user,$db_pass,$db_name);

// add form-fields

$perpetratorsFilterForm->addField('mpg_project', CHECKBOX, -1)
  ->setLabel('Relevant for MPG-Project');

$perpetratorsFilterForm->addField('ID_birth_country', SELECT)
  ->setLabel('Country of Birth')
  ->addOption(NO_VALUE,'all countries')
  ->addOptionsFromTable('nmv__country', 'ID_country', 'english',
                        'EXISTS (	SELECT *
                                  FROM nmv__perpetrator
                                  WHERE nmv__country.ID_country = nmv__perpetrator.ID_birth_country)');

$perpetratorsFilterForm->addField('birth_year', TEXT, 4)
  ->setLabel('Year of Birth (yyyy)');

$perpetratorsFilterForm->addField('br-birth', STATIC_TEXT, '<br>');

$perpetratorsFilterForm->addField('ID_death_country', SELECT)
  ->setLabel('Country of Death')
  ->addOption(NO_VALUE,'all countries')
  ->addOptionsFromTable('nmv__country', 'ID_country', 'english',
                        'EXISTS (	SELECT *
                                  FROM nmv__perpetrator
                                  WHERE nmv__country.ID_country = nmv__perpetrator.ID_death_country)')
  ->addOption('NULL', 'NULL');

$perpetratorsFilterForm->addField('death_year', TEXT, 4)
  ->setLabel('Year of Death (yyyy)');

$perpetratorsFilterForm->addField('died_before_end_of_war', CHECKBOX, -1)
  ->setLabel('Died before June 1945');

$perpetratorsFilterForm->addField('br-death', STATIC_TEXT, '<br>');

$perpetratorsFilterForm->addField('gender', SELECT)
  ->setLabel('Gender')
  ->addOption(NO_VALUE, 'all gender')
  ->addOptionsFromArray(['male' => 'male', 'female' => 'female']);

$perpetratorsFilterForm->addField('ID_religion', SELECT)
  ->setLabel('Religion')
  ->addOption(NO_VALUE,'all religions')
//  ->addOption ('NULL', 'no entry')
  ->addOptionsFromTable('nmv__religion', 'ID_religion', 'english', 'EXISTS (SELECT * FROM nmv__perpetrator
              WHERE nmv__religion.ID_religion = nmv__perpetrator.ID_religion)');

$perpetratorsFilterForm->addField('ID_nationality_1938', SELECT)
  ->setLabel('Nationality in 1938')
  ->addOption(NO_VALUE,'all nationalities')
  ->addOptionsFromTable('nmv__nationality', 'ID_nationality', 'english', 'EXISTS (SELECT * FROM nmv__perpetrator
              WHERE nmv__nationality.ID_nationality = nmv__perpetrator.ID_nationality_1938)');

$perpetratorsFilterForm->addField('separator-qualification', STATIC_TEXT, '<br><hr>');
$perpetratorsFilterForm->addField('text-qualification', STATIC_TEXT, '<h3>Qualification and Career</h3><br>');

$perpetratorsFilterForm->addField('titles', TEXT, 100)
  ->setLabel('Titles (keyword)');

$perpetratorsFilterForm->addField('qualification_place', TEXT, 100)
 ->setLabel('Place of qualification (keyword)');

$perpetratorsFilterForm->addField('qualification_year', TEXT, 4)
 ->setLabel('Year of qualification (yyyy)');

$perpetratorsFilterForm->addField('thesis_title', TEXTAREA, 2)
  ->setLabel('Title of dissertation (keyword)');

$perpetratorsFilterForm->addField('occupation', TEXT, 100)
  ->setLabel('Occupation (keyword)');

$perpetratorsFilterForm->addField('ID_perp_class', SELECT)
  ->setLabel('Classification e.g. physician')
  ->addOption(NO_VALUE, 'all classifications')
  ->addOptionsFromTable('nmv__perpetrator_classification', 'ID_perp_class', 'english');

$perpetratorsFilterForm->addField('career_history', TEXT, 100)
  ->setLabel('Career history (keyword)');

$perpetratorsFilterForm->addField('career_after_1945', TEXT, 100)
  ->setLabel('Career after 1945 (keyword)');

$perpetratorsFilterForm->addField('separator-memberships', STATIC_TEXT, '<br><hr>');
$perpetratorsFilterForm->addField('text-memberships', STATIC_TEXT, '<h3>Memberships</h3><br>');

$perpetratorsFilterForm->addField('leopoldina_member', CHECKBOX, -1)
  ->setLabel('Leopoldina member');

$perpetratorsFilterForm->addField('nsdap_member', CHECKBOX, -1)
  ->setLabel('NSDAP member');

$perpetratorsFilterForm->addField('ss_member', CHECKBOX, -1)
  ->setLabel('SS member');

$perpetratorsFilterForm->addField('sa_member', CHECKBOX, -1)
  ->setLabel('SA member');

$perpetratorsFilterForm->addField('other_nsdap_organisations_member', CHECKBOX, -1)
  ->setLabel('Other NS Organisations');

$perpetratorsFilterForm->addField('details_all_memberships', TEXT, 100)
  ->setLabel('Details all memberships (keyword)');

$perpetratorsFilterForm->addField('separator-information', STATIC_TEXT, '<br><hr>');
$perpetratorsFilterForm->addField('text-information', STATIC_TEXT, '<h3>Search for perpetrators with information in:</h3><br>');

$perpetratorsFilterForm->addField('prosecution-info', CHECKBOX, -1)
  ->setLabel('Field "Prosecution"');

$perpetratorsFilterForm->addField('prison_time-info', CHECKBOX, -1)
  ->setLabel('Field "Prison time"');

$perpetratorsFilterForm->addField('text-keyword', STATIC_TEXT, '<br><br><h3>Search for keyword in:</h3><br>');

$perpetratorsFilterForm->addField('prosecution', TEXT, 50)
  ->setLabel('Field "Prosecution" (keyword)');

$perpetratorsFilterForm->addField('prison_time', TEXT, 50)
  ->setLabel('Field "Prison time" (keyword)');

$perpetratorsFilterForm->addField('notes', TEXT, 100)
  ->setLabel('Field "Notes" (keyword)');

$perpetratorsFilterForm->addField('distance-freetext', STATIC_TEXT, '<br>');


$perpetratorsFilterForm->addField('freetext-fields', TEXT, 50)
  ->setLabel('MULTIPLE freetext fields');

$perpetratorsFilterForm->addField('text-freetext', STATIC_TEXT, 'searches for a keyword in the freetext-fields:  Occupation, Career history, Details All Memberships, Career after 1945, Prosecution, Prison time, Notes');

$perpetratorsFilterForm->addField('separator-end', STATIC_TEXT, '<br><hr>');





// add buttons
$perpetratorsFilterForm
	->addButton(BACK)
	->addButton(RESET)
	->addButton(SUBMIT,L_SEARCH);

?>
