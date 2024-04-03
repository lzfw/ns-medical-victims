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


$form = new Form ('nmv_edit_victim_other_names');

// query: get victim data
$victim_id = (int) getUrlParameter('ID_victim', 0);
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
    $name_id = (int) getUrlParameter('ID_name', 0);
    $querystring = "
    SELECT CONCAT(v.surname, ' ', v.first_names) victim_name,
        v.ID_victim victim_id
    FROM nmv__victim v
    RIGHT JOIN nmv__victim_name vn ON (vn.ID_victim = v.ID_victim)
    WHERE ID_name = $name_id";
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
	->setLabel('Names of ' . $victim_name);

$form
	->addConnection (MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
	->setPrimaryKeyName('ID_name');

$form->addField ('ID_victim',PROTECTED_TEXT)
    ->setLabel ('ID person');
$form->addField ('ID_nametype',SELECT)
    ->setLabel ('name type')
    ->addOption (NO_VALUE,'please choose')
    ->addOptionsFromTable ( 'nmv__victim_nametype', 'ID_nametype', "nametype");
$form->addField ('surname',TEXT,250)
    ->setClass ('keyboardInput')
    ->setLabel ('name');
$form->addField ('first_names',TEXT,50)
    ->setClass ('keyboardInput')
    ->setLabel ('first names');

$form
	->addButton (SUBMIT)
	->addButton (APPLY);

$form
	->addAction (DATABASE,'nmv__victim_name')
	->addAction (REDIRECT,'nmv_view_victim?ID_victim='.$victim_id);

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Victims','nmv_list_victims');
$dbi->addBreadcrumb ('Other names of '.$victim_name,'nmv_view_victim?ID_victim='.$victim_id);

$layout
	->set('title',getUrlParameter('ID_name') ? 'Edit Other Name' : 'New Name')
	->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
	->cast();
