<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$dbi->setUserVar ('ID_source',getUrlParameter('ID_source'),NULL);
$source_id = (int) getUrlParameter('ID_source',0);

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Sources','nmv_list_sources');

// query: get source data
$querystring = "
    SELECT s.source_title, s.signature, s.creation_year, s.pages, s.type,
    s.description, m.medium, s.published_source, s.notes, i.institution_name,
    s.location, s.language, s.url,
    IF((s.access_day IS NULL AND s.access_month IS NULL AND s.access_year IS NULL), ' ', CONCAT(IFNULL(s.access_day, '-'), '.', IFNULL(s.access_month, '-'), '.', IFNULL(s.access_year, '-'))) as access_date,
    s.visibility AS visibility
    FROM nmv__source s
    LEFT JOIN nmv__institution i ON (i.ID_institution = s.ID_institution)
    LEFT JOIN nmv__medium m ON (m.ID_medium = s.ID_medium)
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
    $source_name = $source->source_title . '(' . $source->creation_year . ')';

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
        buildDataSheetRow('Institution',          $source->institution_name).
        buildDataSheetRow('Notes',                $source->notes).
        buildDataSheetRow('URL',                  $source->url).
        buildDataSheetRow('Access date (dmy)',    $source->access_date).
        buildDataSheetRow('Visibility on Website',    $source->visibility)

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
$content .= createButton("Victims",'nmv_list_victim_source?ID_source='.$source_id.'&role=victim','icon report-paper');
$content .= createButton("Prisoner Assistants",'nmv_list_victim_source?ID_source='.$source_id.'&role=prisoner_assistant','icon report-paper');
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
