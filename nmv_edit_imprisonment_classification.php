<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('edit');

$form = new Form ('nmv_imprisonment_classification');

$ID_imprisonment = (int) getUrlParameter('ID_imprisonment', 0);
$ID_victim = (int) getUrlParameter('ID_victim', 0);

// query: get array of classification for this imprisonment
$tagged = $dbi->connection->query("SELECT ic.ID_classification
                                   FROM nmv__imprisonment_classification ic
                                   WHERE ic.ID_imprisonment = $ID_imprisonment");
while ($tag = $tagged->fetch_row()) {
	$tag_array[] = $tag[0];
}

//create create form
$form
	->addConnection(MYSQL_DB, $db_host, $db_user, $db_pass, $db_name)
	->setPrimaryKeyName('ID_imp_classification');

$form
  ->addField('ID_imprisonment', HIDDEN);

$form
  ->addField('ID_classification', MULTICHECKBOX)
  ->setLabel('Classification')
  ->addOptionsFromTable('nmv__victim_classification', 'ID_classification', 'english', $tag_array);

$form
  ->addButton(SUBMIT, 'Update Tags');

$form
	->addAction(DATABASE, 'nmv__imprisonment_classification', 'tag')
  ->addAction(REDIRECT, "nmv_edit_victim_imprisoniation?ID_imprisoniation={ID_imprisonment}&ID_victim=" . $ID_victim);

$dbi->addBreadcrumb(L_CONTENTS, 'z_menu_contents');
$dbi->addBreadcrumb('Imprisonment');

$layout
	->set('title', 'Classification(s) for Imprisonment ID ' . $ID_imprisonment)
	->set('content', '<div>please select all classifications and then click button "Update Tags"</div>' . $form->run() . '<div class="message">' . $form->success_message . '</div>' . $form->debuglog->Show())
	->cast();
