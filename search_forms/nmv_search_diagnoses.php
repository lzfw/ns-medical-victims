<?php
/**
* creates form for search for diagnosis keyword in different columns
*
*
*
*/

// create form
$diagnosesForm = new Form ('search_diagnoses','nmv_result_diagnoses.php','GET');

// establish database-connection
$diagnosesForm->addConnection(MYSQL_DB,$db_host,$db_user,$db_pass,$db_name);

// add form-fields

$diagnosesForm->addField ('keyword',TEXT,120)
	->setLabel ('keyword for diagnoses');

// add buttons
$diagnosesForm
	->addButton (BACK)
	->addButton (RESET)
	->addButton (SUBMIT,L_SEARCH);
?>
