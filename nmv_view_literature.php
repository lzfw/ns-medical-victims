<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$dbi->setUserVar ('ID_literature',getUrlParameter('ID_literature'),NULL);
$literature_id = (int) getUrlParameter('ID_literature',0);

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Literature','nmv_list_literature');

// query: get literature data
$querystring = "
    SELECT authors, lit_year, lit_title, article, journal_or_series,
           editor, volume, location, pages, publisher, scientific_exploitation, written_by_perpetrator,
           notes, url, isbn, doi,
           IF((access_day IS NULL AND access_month IS NULL AND access_year IS NULL), ' ', CONCAT(IFNULL(access_day, '-'), '.', IFNULL(access_month, '-'), '.', IFNULL(access_year, '-'))) as access_date
    FROM nmv__literature v
    WHERE ID_literature = ?";

$result = null;
if ($stmt = $dbi->connection->prepare($querystring)) {
    if ( $stmt->bind_param('i', $literature_id) ) {
        if ( $stmt->execute() ) {
            $result = $stmt->get_result();
        } else {
            throw new RuntimeException("Can not execute query: " .
                implode(': ', $stmt->error_list) .
                ' / #' . $stmt->errno . ' / ' . $stmt->error);
        }
    } else {
        throw new RuntimeException("Can not bind ID parameter: " .
            implode(': ', $stmt->error_list) .
            ' / #' . $stmt->errno . ' / ' . $stmt->error);
    }
} else {
    var_dump($dbi->connection->error);
    throw new RuntimeException("Can not prepare query: " .
        implode(': ', $dbi->connection->error_list) .
        ' / #' . $dbi->connection->errno . ' / ' . $dbi->connection->error);
}

if ($literature = $result->fetch_object()) {
    $literature_name = '“' . $literature->lit_title.'” '.$literature->authors;
    $content = buildElement('table','grid',
        buildDataSheetRow('Literature ID',                $literature_id).
        buildDataSheetRow('Authors',                      $literature->authors).
        buildDataSheetRow('Year',                         $literature->lit_year).
        buildDataSheetRow('Title',                        $literature->lit_title).
        buildDataSheetRow('Is Article',                   $literature->article ? 'Yes' : 'No').
        buildDataSheetRow('Journal or Edited Volume',     $literature->journal_or_series).
        buildDataSheetRow('Article Editor',               $literature->editor).
        buildDataSheetRow('Article Volume',               $literature->volume).
        buildDataSheetRow('Location',                     $literature->location).
        buildDataSheetRow('Article Pages',                $literature->pages).
        buildDataSheetRow('Publisher',                    $literature->publisher).
        buildDataSheetRow('Scientific exploitation',
            $literature->scientific_exploitation ? 'Yes' : 'No').
        buildDataSheetRow('Written by perpetrator',
            $literature->written_by_perpetrator ? 'Yes (at least one author is listed as perpetrator in this database)' : 'No').
        buildDataSheetRow('Notes',                        $literature->notes).
        buildDataSheetRow('URL',                          $literature->url).
        buildDataSheetRow('Access Data (dmy)',            $literature->access_date).
        buildDataSheetRow('ISBN',                      $literature->isbn).
        buildDataSheetRow('DOI',                      $literature->doi)
    );
} else {
    $literature_name = 'Error: unknown literature';
    $content = buildElement('p','Error: Literature not found. Maybe it has been deleted from the database?');
}

$content .= '<div class="buttons">';
if ($dbi->checkUserPermission('edit'))
    $content .= createButton ('Edit','nmv_edit_literature?ID_literature='.$literature_id,'icon edit');
if ($dbi->checkUserPermission('admin'))
    $content .= createButton(L_DELETE,'nmv_remove_literature?ID_literature='.$literature_id,'icon delete');
$content .= createButton("Victims",'nmv_list_victim_literature?ID_literature='.$literature_id.'&role=victim','icon report-paper');
$content .= createButton("Prisoner Assistants",'nmv_list_victim_literature?ID_literature='.$literature_id.'&role=prisoner_assistant','icon report-paper');
$content .= createButton("Perpetrators",'nmv_list_perpetrator_literature?ID_literature='.$literature_id,'icon report-paper');
//complete db d
if(!($dbi->checkUserPermission('mpg'))):
  $content .= createButton("Biomedical Research",'nmv_list_experiment_literature?ID_literature='.$literature_id,'icon report-paper');
endif;
$content .= '</div><br>';

$content .= createButton ('BACK','javascript:history.back()');


$layout
	->set('title','Literature: '.$literature_name)
	->set('content',$content)
	->cast();
