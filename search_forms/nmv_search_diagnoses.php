<?php
/**
* creates form for search for diagnosis keyword in different columns
*
*
*
*/

// create form
$diagnosesForm = new Form('search_diagnoses','nmv_result_diagnoses.php','GET');

// establish database-connection
$diagnosesForm->addConnection(MYSQL_DB,$db_host,$db_user,$db_pass,$db_name);

// add form-fields

$diagnosesForm->addField('keyword',TEXT,120)
	->setLabel('search for keyword');
$diagnosesForm->addField('keyword_info', STATIC_TEXT, 'Returns victims with given keyword in: <br>cause of death, hospitalisation diagnosis, brain report diagnosis (in tags and/or freetext) or hospitalisation notes / autopsy details<hr>');
$diagnosesForm->addField('diagnosis_tag', SELECT)
	->setLabel('search in diagnosis tags only')
	->addOption(NO_VALUE, 'please choose diagnosis tag')
	->addOptionsFromTable('nmv__diagnosis_tag', 'ID_diagnosis', 'diagnosis');
$diagnosesForm->addField('tag_info', STATIC_TEXT, 'Returns victims with choosen diagnosis tag in: <br>hospitalisation diagnosis tags and/or brain report diagnosis tags (not in freetext diagnosis)<hr>');


// add buttons
$diagnosesForm
	->addButton(BACK)
	->addButton(RESET)
	->addButton(SUBMIT,L_SEARCH);
?>
