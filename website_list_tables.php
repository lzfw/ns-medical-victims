<?php

require_once 'zefiro/ini.php';

$dbi->requireUserPermission('admin');

$dbi->addBreadcrumb(L_ADMIN, 'z_menu_admin');

$content = '<div>
                <br>The Website <i>?URL?</i> contains several different kinds of Pages to represent 
                the data of the ns-medical-victims database in multiple tables. <br><br>
                There are two different kinds of Pages.  
                <ul>
                    <li><strong>Index Page:</strong> contains one table with all database entries of one kind 
                    (index list for Persons, Institutions, Sources, Literature or Experiments)</li>
                    <li><strong>Entry Page:</strong> contains a full entry.
                        That includes tables with the entity data itself (entity data, entity subtables) 
                        and tables with entries of linked information (entity link list).  
                    </li>
                </ul>
                <br>                
                In the following sections you find lists of all website-tables. <br>
                When following the respective link to the table details, you will find information about the table contents.<br>
                That means the different categories (rows/columns) in the table. <br>
                You will also find the option to edit visibility and explanatory texts for the complete table as well
                as for the single categories.<br>               
            </div>';

// SELECT QUERIES FOR ALL TABLES
$general_query = "SELECT t.ID_website_table, t.website_table, t.type_of_table, t.ID_website_page, p.website_page, t.visibility, t.info
                 FROM website_table t
                 LEFT JOIN website_page p ON p.ID_website_page = t.ID_website_page"; // für Ergebnisliste

$queries = [
    'index' => $dbi->connection->query($general_query . " WHERE type_of_table = 'index list'"),
    'victim' => $dbi->connection->query($general_query . " WHERE t.ID_website_page = 8"),
    'prisoner_assistant' => $dbi->connection->query($general_query . " WHERE t.ID_website_page = 9"),
    'experiment' => $dbi->connection->query($general_query . " WHERE t.ID_website_page = 10"),
    'institution' => $dbi->connection->query($general_query . " WHERE t.ID_website_page = 11"),
    'source' => $dbi->connection->query($general_query . " WHERE t.ID_website_page = 12"),
    'literature' => $dbi->connection->query($general_query . " WHERE t.ID_website_page = 13"),
    'perpetrator' => $dbi->connection->query($general_query . " WHERE t.ID_website_page = 14"),
];

// Function to generate HTML for tables
function generateTableHtml($title, $description, $query, $dbi) {
    $content = "<div class='margintop3'>";
    $content .= "<h3>$title</h3>";
    $content .= "<p>$description</p>";
    $content .= '<table class="grid">';
    $content .= '<tr>
                    <th>Table</th><th>Type of Table</th><th>Webpage</th><th>Webpage ID</th><th>Visibility</th><th>Info Text for Website</th><th>ID</th><th>Options</th>
                </tr>';
    while ($entry = $query->fetch_object()) {
        $content .= '<tr>';
        $content .= '<td class="nowrap"><a href="website_view_table?ID_website_table=' . $entry->ID_website_table . '">' . htmlentities($entry->website_table, ENT_HTML5) .  '</a></td>';
        $content .= "<td class='nowrap'>$entry->type_of_table</td>";
        $content .= "<td class='nowrap'>$entry->website_page</td>";
        $content .= "<td class='nowrap'>$entry->ID_website_page</td>";
        $content .= "<td class='nowrap'>$entry->visibility</td>";
        $content .= "<td class='minwidth30'>$entry->info</td>";
        $content .= "<td class='nowrap'>$entry->ID_website_table</td>";
        $content .= '<td class="nowrap">';
        $content .= createSmallButton('View Table Details','website_view_table?ID_website_table='.$entry->ID_website_table,'icon view');
        $content .= "</td>";
        $content .= '</tr>';
    }
    $content .= '</table>';
    $content .= '</div>';

    return $content;
}

// Generate the HTML for each section
$content .= generateTableHtml('Index Pages',
    'Every Table lists names / titles and basic data of all datasets of one kind. Website-Table can be sorted and filtered.
    <br> Click "Table"-link or "View Table Details"-button for more information about the table and its columns and options to alter visibility and info-texts',
    $queries['index'],
    $dbi);

$content .= generateTableHtml('Victim Entry Page',
    'Shows a full database profile for a person that was victim of unethical research. 
    There are tables that contain the persons biographical data and tables with other database entities, the person is linked to.
    <br>
    Click "Table"-link or "View Table Details"-button for more information about the table and its columns and options to alter visibility and info-texts',
    $queries['victim'],
    $dbi);

$content .= generateTableHtml('Prisoner Assistant Entry Page',
    'Shows a full database profile for a person that was involved in unethical research as a prisoner assistant. 
    There are tables that contain the persons biographical data and tables with other database entities, the person is linked to.
    <br> 
    Click "Table"-link or "View Table Details"-button for more information about the table and its columns and options to alter visibility and info-texts',
    $queries['prisoner_assistant'],
    $dbi);

$content .= generateTableHtml('Experiment Entry Page',
    'Shows a full entry for an experiment. There is a table that contains the experiment data and tables 
    with other database entities, the experiment is linked to.
    <br> 
    Click "Table"-link or "View Table Details"-button for more information about the table and its columns and options to alter visibility and info-texts',
    $queries['experiment'],
    $dbi);

$content .= generateTableHtml('Institution Entry Page',
    'Shows a full entry for an institution. There is a table that contains the institution data and tables 
    with other database entities, the institution is linked to.
    <br> 
    Click "Table"-link or "View Table Details"-button for more information about the table and its columns and options to alter visibility and info-texts',
    $queries['institution'],
    $dbi);

$content .= generateTableHtml('Source Entry Page',
    'Shows a full entry for a source. There is a table that contains the source data and tables 
    with other database entities, the source is linked to.
    <br> 
    Click "Table"-link or "View Table Details"-button for more information about the table and its columns and options to alter visibility and info-texts',
    $queries['source'],
    $dbi);

$content .= generateTableHtml('Literature Entry Page',
    'Shows a full entry for a literature. There is a table that contains the bibliographical data and tables 
    with other database entities, the literature is linked to.
    <br> 
    Click "Table"-link or "View Table Details"-button for more information about the table and its columns and options to alter visibility and info-texts',
    $queries['literature'],
    $dbi);

$content .= generateTableHtml('Perpetrator Entry Page',
    'Shows a full database profile for a person that was conducting unethical research. 
    There are tables that contain the perpetrators biographical data and tables with other database entities, the person is linked to.
    <br>
    Click "Table"-link or "View Table Details"-button for more information about the table and its columns and options to alter visibility and info-texts',
    $queries['perpetrator'],
    $dbi);


$layout
    ->set('title', 'Website Tables')
    ->set('content',$content
        . createBackLink(L_ADMIN, 'z_menu_admin')
    )	->set('sidebar',$dbi->getTextblock_HTML ('z_database'))

    ->cast();
