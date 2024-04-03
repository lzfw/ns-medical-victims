<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

/** Requires check of User permission - User needs to have permission to edit.
 * If profile is from TEilprojekte Berlin/Vienna/Munich special permission 'all' is needed
 *
 * @var DBI $dbi Calls method require user permission.
 * @see
 */
//OBACHT
$ID_victim = (int) getUrlParameter('ID_victim', 0);
if($ID_victim >= 46028 && $ID_victim <= 46126 || $ID_victim >= 46259 && $ID_victim <= 47647):
    $dbi->requireUserPermission('all');
else:
    $dbi->requireUserPermission ('edit');
endif;
$form = new Form ('nmv_med_hist_diagnosis');

// query: get victim data
$victim_id = (int) getUrlParameter('ID_victim', 0);
$med_id = (int) getUrlParameter('ID_med_history_diagnosis', 0);

$victim_name = 'Error: Unknown.';
if ($victim_id) {
    $querystring = "
    SELECT CONCAT(surname, ' ', first_names) victim_name
    FROM nmv__victim
    WHERE ID_victim = $victim_id";
    $query = $dbi->connection->query($querystring);
    $victim = $query->fetch_object();
    $victim_name = $victim->victim_name;
} else {
    $querystring = "
    SELECT CONCAT(v.surname, ' ', v.first_names) victim_name,
        v.ID_victim victim_id
    FROM nmv__victim v
    RIGHT JOIN nmv__med_history_diagnosis d ON (d.ID_victim = v.ID_victim)
    WHERE d.ID_med_history_diagnosis = $med_id";
    $query = $dbi->connection->query($querystring);
    $victim = $query->fetch_object();
    $victim_id = $victim->victim_id;
    //OBACHT
    if($victim_id>= 46028 && $victim_id <= 46126 || $victim_id >= 46259 && $victim_id <= 47647):
        $dbi->requireUserPermission('all');
    else:
        $dbi->requireUserPermission ('edit');
    endif;
    $victim_name = $victim->victim_name;
}


$form
	->setLabel('Diagnosis of ' . $victim_name);

$form
	->addConnection(MYSQL_DB, $db_host, $db_user, $db_pass, $db_name)
	->setPrimaryKeyName('ID_med_history_diagnosis');

$form->addField('ID_victim', PROTECTED_TEXT)
    ->setLabel('Victim ID');
$form->addField('diagnosis', TEXTAREA, REQUIRED)
    ->setLabel('Diagnosis');
$form->addField('year', TEXT, 4)
    ->setLabel('Year YYYY of diagnosis')
    ->addCondition(VALUE ,MIN, 1846)
    ->addCondition(VALUE, MAX, 1945);
$form->addField('info', STATIC_TEXT, '<hr><strong>Diagnosis Tags </strong> can be edited in the Diagnosis View. <br>
    Click OK-Button in order to save your changes and switch to View.<hr>');

$form
	->addButton(SUBMIT)
	->addButton(APPLY);

$form
	->addAction(DATABASE, 'nmv__med_history_diagnosis')
	->addAction(REDIRECT, 'nmv_view_med_hist_diagnosis?ID_med_history_diagnosis={ID_med_history_diagnosis}');

$dbi->addBreadcrumb(L_CONTENTS, 'z_menu_contents');
$dbi->addBreadcrumb('Victims', 'nmv_list_victims');
$dbi->addBreadcrumb('Medical History of '.$victim_name, 'nmv_list_med_diagnosis?ID_victim='.$victim_id);

$layout
	->set('title',getUrlParameter('ID_med_history_diagnosis') ? 'Edit Diagnosis' : 'New Diagnosis')
	->set('content', $form->run() . '<div class="message">' . $form->success_message . '</div>' . $form->debuglog->Show())
	->cast();
