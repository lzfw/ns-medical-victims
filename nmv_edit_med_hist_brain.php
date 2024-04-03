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

$form = new Form ('nmv_med_hist_brain');

// query: get victim data
$victim_id = (int) getUrlParameter('ID_victim', 0);
$med_id = (int) getUrlParameter('ID_med_history_brain', 0);

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
    RIGHT JOIN nmv__med_history_brain h ON (h.ID_victim = v.ID_victim)
    WHERE ID_med_history_brain = $med_id";
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
	->setLabel('Brain report related to victim ' . $victim_name);

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_med_history_brain');

$form->addField ('ID_victim',PROTECTED_TEXT)
    ->setLabel ('Victim ID');
$form->addField ('ID_institution',SELECT,REQUIRED)
    ->setLabel ('Research Institution')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromTable ( 'nmv__institution', 'ID_institution', "CONCAT(IFNULL(institution_name, '-/-'), '&emsp;---&emsp;', LEFT(IFNULL(location, '-/-'), 50))");
$form->addField ('kwi_researcher',TEXT,150)
    ->setClass ('keyboardInput')
    ->setLabel ('KWI Researcher');
$form->addField ('diagnosis',TEXTAREA)
    ->setLabel('Brain Diagnosis');
$form->addField ('brain_report_day',TEXT,2)
    ->setLabel ('Brain Report Date DMYYYY')
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,31);
$form->addField ('brain_report_month',TEXT,2)
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,12)
    ->appendTo('brain_report_day');
$form->addField ('brain_report_year',TEXT,4)
    ->addCondition(VALUE,MIN,0)
    ->addCondition(VALUE,MAX,1960)
    ->appendTo('brain_report_day');
$form->addField ('notes',TEXTAREA)
    ->setClass ('keyboardInput')
    ->setLabel ('Notes');
$form->addField ('ref_no',TEXT,50)
    ->setLabel ('Reference number');
$form->addField ('ref_no_2',TEXT,50)
    ->setLabel ('2nd Reference number');
$form->addField('brain_report_has_photo', CHECKBOX, -1)
    ->setLabel('brain report contains photo');
$form->addField('info', STATIC_TEXT, '<hr><strong>Diagnosis Tags </strong> can be edited in the Brain Report View. <br>
Click OK-Button in order to save your changes and switch to View.<hr>');

$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__med_history_brain')
	->addAction (REDIRECT,'nmv_view_med_hist_brain?ID_med_history_brain={ID_med_history_brain}');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Victims','nmv_list_victims');
$dbi->addBreadcrumb ('Medical History of '.$victim_name,'nmv_list_med_hist?ID_victim='.$victim_id);

$layout
	->set('title',getUrlParameter('ID_med_history_brain') ? 'Edit Brain Report' : 'New Brain Report')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
