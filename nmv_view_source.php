<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$dbi->setUserVar ('ID_source',getUrlParameter('ID_source'),NULL);
$source_id = (int) getUrlParameter('ID_source',0);

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Source','nmv_list_sources');

// query: get source data
$querystring = "
    SELECT source_title, signature, creation_year, pages, type,
    description, medium, published_source, notes,
    data_entry_status, names_mentioned, location, person_in_charge,
    english as language, url,
    IF((access_day IS NULL AND access_month IS NULL AND access_year IS NULL), ' ', CONCAT(IFNULL(access_day, '-'), '.', IFNULL(access_month, '-'), '.', IFNULL(access_year, '-'))) as access_date
    FROM nmv__source s
    LEFT JOIN nmv__language l ON (l.ID_language = s.ID_language)
    WHERE ID_source = ?";

$result = null;
if ($stmt = $dbi->connection->prepare($querystring)) {
    if ( $stmt->bind_param('i', $source_id) ) {
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

if ($source = $result->fetch_object()) {
    $source_name = '“' . $source->source_title.'” ('.$source->creation_year . ')';

    $content = buildElement('table','grid',
        buildDataSheetRow('Source ID',            $source_id).
        buildDataSheetRow('Title',                $source->source_title).
        buildDataSheetRow('Signature',            $source->signature).
        buildDataSheetRow('Creation year',        $source->creation_year).
        buildDataSheetRow('Pages',                $source->pages).
        buildDataSheetRow('Type',                 $source->type).
        buildDataSheetRow('Language',             $source->language).
        buildDataSheetRow('Description',          $source->description).
        buildDataSheetRow('Medium',               $source->medium).
        buildDataSheetRow('Published source',     $source->published_source ? 'Yes' : 'No').
        buildDataSheetRow('Location',             $source->location).
        buildDataSheetRow('Notes',                $source->notes).
        buildDataSheetRow('URL',                  $source->url).
        buildDataSheetRow('Access date (dmy)',    $source->access_date)
    );
} else {
    $source_name = 'Error: unknown source';
    $content = buildElement('p','Error: Source not found. Maybe it has been deleted from the database?');
}

$content .= '<div class="buttons">';
if ($dbi->checkUserPermission('edit'))
    $content .= createButton ('Edit Source','nmv_edit_source?ID_source='.$source_id,'icon edit');
if ($dbi->checkUserPermission('admin'))
    $content .= createButton(L_DELETE,'nmv_remove_source?ID_source='.$source_id,'icon delete');
$content .= createButton("Victims",'nmv_list_victim_source?ID_source='.$source_id,'icon report-paper');
$content .= createButton("Perpetrators",'nmv_list_perpetrator_source?ID_source='.$source_id,'icon report-paper');
//complete db d
if (!($dbi->checkUserPermission('mpg'))) :
  $content .= createButton("Biomedical Research",'nmv_list_experiment_source?ID_source='.$source_id,'icon report-paper');
endif;
$content .= '</div><br>';

$content .= createButton ('BACK','javascript:history.back()');


$layout
	->set('title','Source: '.$source_name)
	->set('content',$content)
	->cast();
