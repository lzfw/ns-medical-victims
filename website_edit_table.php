<?php
require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission('admin');

//Get data from parent table
$ID_website_table = (int) getUrlParameter('ID_website_table', 0);
$querystring = "SELECT p.website_page AS website_page
                FROM website_table t 
                LEFT JOIN website_page p ON p.ID_website_page = t.ID_website_page
                WHERE t.ID_website_table = $ID_website_table";
$table_data = $dbi->connection->query($querystring)->fetch_object();
$website_page = $table_data->website_page;

$form = new Form('website_edit_table');

$form->setLabel('Website Table on: ');
$form->addField('webpage', STATIC_TEXT, "Page: $website_page");
$form
    ->addConnection(MYSQL_DB,$db_host,$db_user,$db_pass,$db_name)
    ->setPrimaryKeyName('ID_website_table');
$form->addfield('ID_website_table', PROTECTED_TEXT)
    ->setLabel('ID of Table');
$form->addField('website_table', PROTECTED_TEXT)
    ->setLabel('Name of Table');
$form->addField('type_of_table', PROTECTED_TEXT)
    ->setLabel('Type of Table');
$form->addField('visibility', SELECT)
    ->setLabel('Visibility')
    ->addOption('public')
    ->addOption('restricted')
    ->addOption('hidden');
$form->addField('info', TEXTAREA)
    ->setClass('keyboardInput')
    ->setLabel('Info Text');

$form
    ->addButton (SUBMIT);

$form
    ->addAction (DATABASE,'website_table')
    ->addAction (REDIRECT,'website_view_table?ID_website_table={ID_website_table}');

$dbi->addBreadcrumb (L_ADMIN,'z_menu_admin');
$dbi->addBreadcrumb ('List Website Tables','website_list_tables');
$dbi->addBreadcrumb("Website Table - ID $ID_website_table", "website_view_table?ID_website_table=$ID_website_table");



$layout
    ->set('title','Edit Website Table')
    ->set('content',$form->run().'<div class="message">'.$form->success_message.'</div>'.$form->debuglog->Show())
    ->cast();