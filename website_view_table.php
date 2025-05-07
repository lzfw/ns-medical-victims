<?php


require_once 'zefiro/ini.php';

$dbi->requireUserPermission('admin');

$dbi->setUserVar ('ID_website_table',getUrlParameter('ID_website_table'),NULL);
$id_website_table = (int) getUrlParameter('ID_website_table',0);

$dbi->addBreadcrumb(L_ADMIN, 'z_menu_admin');
$dbi->addBreadcrumb ('List Website Tables','website_list_tables');


$content = '<br>';


// SELECT QUERIES FOR ALL Data Categories
$table_query = "SELECT t.website_table, p.website_page, t.type_of_table, t.visibility AS visibility_table, t.info AS info_table, t.ID_website_table
                    FROM website_table t 
                    LEFT JOIN website_page p ON p.ID_website_page = t.ID_website_page
                    WHERE t.ID_website_table = $id_website_table";
$table_query_result = $dbi->connection->query($table_query);

$data_query = "SELECT d.ID_website_table_data, d.website_table_data, d.visibility AS visibility_data, d.info AS info_data,
                        d.ID_website_table, t.website_table AS website_table
                     FROM website_table_data d
                     LEFT JOIN website_table t ON t.ID_website_table = d.ID_website_table                     
                     WHERE d.ID_website_table = $id_website_table"; //für Ergebnisliste
$data_query_result = $dbi->connection->query($data_query);
$table = $table_query_result->fetch_object();

// HTML FOR TABLE
$content .= '<div>';
$content .= "<h3>Table: '$table->website_table'<br>Website Location: '$table->website_page'</h3>";
$content .= '<p>
If visibility is set to public, the table is visible for every visitor of the website. 
If it is set to restricted, it is only visible after login.<br>
In order to change visibility or the info text for the COMPLETE table, edit the entry.
In order to change visibility or the infotext for only a part of the table, edit the correpsonding content of the table in the next section. </p>';
$content .= '<table class="grid">';
$content .= '<tr>
                <th>Name of Table</th><th>Webpage</th><th>Type of Table</th><th>Visibility</th><th>Info</th><th>ID Table</th><th>Options</th>
            </tr>';
$content .= '<tr>';
$content .= "<td class='nowrap'>$table->website_table</td>";
$content .= "<td class='nowrap'>$table->website_page</td>";
$content .= "<td class='nowrap'>$table->type_of_table</td>";
$content .= "<td class='nowrap'>$table->visibility_table</td>";
$content .= "<td class='minwidth30'>$table->info_table</td>";
$content .= "<td class='nowrap'>$table->ID_website_table</td>";
$content .= '<td class="nowrap">';
$content .= createSmallButton(L_EDIT, 'website_edit_table?ID_website_table=' . $table->ID_website_table, 'icon edit');
$content .= "</td>";
$content .= '</tr>';
$content .= '</table>';
$content .= '</div>';



// HTML FOR TABLE CONTENT (rows / columns)
$content .= '<div class="margintop3">';
$content .= "<h3>Contents of Table: '$table->website_table'</h3>";
$content .= '<p>Each Entry represents a data category of the table. Depending on the table this is a row or a column of the table
If visibility is set to public, the category is visible for every visitor of the website. 
If it is set to restricted, it is only visible after login.<br><br>
In order to change visibility or the info text, edit the corresponding entry. <br>
Please note: if table visibility is set to restricted, contents will be hidden to, independently of the visibility setting here.</p>';
$content .= '<table class="grid">';
$content .= '<tr>
                <th>Name of Data (Column or Row)</th><th>Visibility</th><th>Info</th><th>ID Data</th><th>Options</th>
            </tr>';
while ($entry = $data_query_result->fetch_object()) {
    $content .= '<tr>';
    $content .= "<td class='nowrap'>$entry->website_table_data</td>";
    $content .= "<td class='nowrap'>$entry->visibility_data</td>";
    $content .= "<td class='minwidth30'>$entry->info_data</td>";
    $content .= "<td class='nowrap'>$entry->ID_website_table_data</td>";
    $content .= '<td class="nowrap">';
    $content .= createSmallButton(L_EDIT, 'website_edit_table_data?ID_website_table_data=' . $entry->ID_website_table_data, 'icon edit');
    $content .= "</td>";
    $content .= '</tr>';
}
$content .= '</table>';
$content .= '</div>';



$layout
    ->set('title', 'View Website Table')
    ->set('content', $content
        . createBackLink('List of Website Tables', 'website_list_tables')
    )->set('sidebar', $dbi->getTextblock_HTML('z_database'))
    ->cast();

