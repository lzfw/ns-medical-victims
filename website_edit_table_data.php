<?php

require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission('admin');

//Get data from parent tables
$ID_website_table_data = (int) getUrlParameter('ID_website_table_data', 0);
$querystring = "SELECT p.website_page AS website_page, t.ID_website_table, t.website_table AS website_table
                FROM website_table_data d 
                LEFT JOIN website_table t ON t.ID_website_table = d.ID_website_table
                LEFT JOIN website_page p ON p.ID_website_page = t.ID_website_page
                WHERE d.ID_website_table_data = $ID_website_table_data";
$table_data = $dbi->connection->query($querystring)->fetch_object();
$website_table = $table_data->website_table;
$website_page = $table_data->website_page;
$ID_website_table = $table_data->ID_website_table;


$form = new Form('website_edit_table_data');
$form->setLabel('Website Table Data in:');
$form->addField('webpage', STATIC_TEXT, "Page: $website_page");
$form->addField('table', STATIC_TEXT, "Table: $website_table");
$form->addConnection(MYSQL_DB, $db_host, $db_user, $db_pass, $db_name)
    ->setPrimaryKeyName('ID_website_table_data');
$form->addfield('ID_website_table_data', PROTECTED_TEXT)
    ->setLabel('ID of Table Data');
$form->addField('website_table_data', PROTECTED_TEXT)
    ->setLabel('Name of Row / Column');
$form->addField('visibility', SELECT)
    ->setLabel('Visibility')
    ->addOption('public')
    ->addOption('restricted')
    ->addOption('hidden');
$form->addField('info', TEXTAREA)
    ->setClass('keyboardInput')
    ->setLabel('Info Text');

$form
    ->addButton(SUBMIT);

$form
    ->addAction(DATABASE, 'website_table_data')
    ->addAction(REDIRECT, "website_view_table?ID_website_table=$ID_website_table");

$dbi->addBreadcrumb(L_ADMIN, 'z_menu_admin');
$dbi->addBreadcrumb('List Website Tables', 'website_list_tables');
$dbi->addBreadcrumb("View Website Table - ID $ID_website_table", "website_view_table?ID_website_table=$ID_website_table");


$layout
    ->set('title', 'Edit Website Table Data')
    ->set('content', $form->run() . '<div class="message">' . $form->success_message . '</div>' . $form->debuglog->Show())
    ->cast();