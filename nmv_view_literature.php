<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$dbi->setUserVar ('ID_literature',getUrlParameter('ID_literature'),NULL);
$literature_id = (int) getUrlParameter('ID_literature',0);

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Literature','nmv_list_literature');

// query: get literature data
$querystring = '
    SELECT authors, lit_year, lit_title, article, journal_or_series,
           editor, volume, location, pages, publisher, scientific_exploitation,
           notes, url, isbn_10, isbn_13
    FROM nmv__literature v
    WHERE ID_literature = ?';

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
        buildDataSheetRow('Authors',                $literature->authors).
        buildDataSheetRow('Year',                   $literature->lit_year).
        buildDataSheetRow('Title',                  $literature->lit_title).
        buildDataSheetRow('Is Article',                     $literature->article ? 'Yes' : 'No').
        buildDataSheetRow('Journal or Edited Volume',      $literature->journal_or_series).
        buildDataSheetRow('Article Editor',                 $literature->editor).
        buildDataSheetRow('Article Volume',                 $literature->volume).
        buildDataSheetRow('Location',                       $literature->location).
        buildDataSheetRow('Article Pages',                  $literature->pages).
        buildDataSheetRow('Publisher',              $literature->publisher).
        buildDataSheetRow('Scientific exploitation',
            $literature->scientific_exploitation ? 'Yes' : 'No').
        buildDataSheetRow('Notes',                  $literature->notes).
        buildDataSheetRow('URL',                    $literature->url).
        buildDataSheetRow('ISBN-10',                  $literature->isbn_10).
        buildDataSheetRow('ISBN_13',                  $literature->isbn_13)
    );
} else {
    $literature_name = 'Error: unknown literature';
    $content = buildElement('p','Error: Literature not found. Maybe it has been deleted from the database?');
}

$content .= '<div class="buttons">';
if ($dbi->checkUserPermission('edit'))
    $content .= createButton ('Edit Literature','nmv_edit_literature?ID_literature='.$literature_id,'icon edit');
if ($dbi->checkUserPermission('admin'))
    $content .= createButton(L_DELETE,'nmv_remove_literature?ID_literature='.$literature_id,'icon delete');
$content .= createButton("Victims",'nmv_list_victim_literature?ID_literature='.$literature_id,'icon report-paper');
$content .= createButton("Perpetrators",'nmv_list_perpetrator_literature?ID_literature='.$literature_id,'icon report-paper');
//complete db d
if(!($dbi->checkUserPermission('mpg')):
  $content .= createButton("Biomedical Research",'nmv_list_experiment_literature?ID_literature='.$literature_id,'icon report-paper');
endif;
$content .= '</div>';

$content .= createBackLink ("List of Literature",'nmv_list_literature');


$layout
	->set('title','Literature: '.$literature_name)
	->set('content',$content)
	->cast();
