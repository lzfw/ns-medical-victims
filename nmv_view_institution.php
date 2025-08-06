<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$dbi->setUserVar ('ID_institution',getUrlParameter('ID_institution'),NULL);
$institution_id = (int) getUrlParameter('ID_institution',0);

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Institutions','nmv_list_institutions');

// query: get institution data
$querystring = '
    SELECT i.ID_institution, i.institution_name, i.location, c.country AS country, t.institution_type, 
           i.latitude, i.longitude, i.notes, i.visibility
    FROM nmv__institution i
    LEFT JOIN nmv__institution_type t ON (t.ID_institution_type = i.ID_institution_type)
    LEFT JOIN nmv__country c ON c.ID_country = i.ID_country
    WHERE i.ID_institution = ?';

$result = null;
if ($stmt = $dbi->connection->prepare($querystring)) {
    if ( $stmt->bind_param('i', $institution_id) ) {
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

$content = '';
$institution_name = 'Unknown institution - Maybe deleted?';
$institution_type = 0;

if ($institution = $result->fetch_object()) {
    $institution_name = $institution->institution_name;
    $content .= buildElement('table','grid',
        buildDataSheetRow('Institution ID',             $institution_id).
        buildDataSheetRow('Name',                       $institution->institution_name).
        buildDataSheetRow('Location',                   $institution->location).
        buildDataSheetRow('Present Country',            $institution->country).
        buildDataSheetRow('Type',                       $institution->institution_type).
        buildDataSheetRow('Geocoordinate - Latitude',   $institution->latitude).
        buildDataSheetRow('Geocoordinate - Longitude',  $institution->longitude).
        buildDataSheetRow('Notes',                      $institution->notes).
        buildDataSheetRow('Visibility on Website',      $institution->visibility)
    );

    $content .= '<div>';
    $content .= '<br>'.buildElement('h3', 'Authority Records');
    // query: get other names
    $querystring = "
    SELECT ari.ID_authority_record_institution AS ID_authority_record_institution, ari.authority_type, ari.authority_id
    FROM nmv__authority_record_institution ari
    WHERE ari.ID_institution = $institution_id";

    $options = '';
    $row_template = ['{authority_type}', '{authority_id}'];
    $header_template = ['Typ', 'ID'];

    if ($dbi->checkUserPermission('edit') || $dbi->checkUserPermission('admin')) {
        if ($dbi->checkUserPermission('edit')) {
            $options .= createSmallButton(L_EDIT,'nmv_edit_authority_record_institution?ID_authority_record_institution={ID_authority_record_institution}','icon edit');
        }
        if ($dbi->checkUserPermission('admin')) {
            $options .= createSmallButton(L_DELETE,'nmv_remove_authority_record_institution?ID_authority_record_institution={ID_authority_record_institution}','icon delete');
        }
        $row_template[] = $options;
        $header_template[] = L_OPTIONS;
    }

    $content .= buildTableFromQuery(
        $querystring,
        $row_template,
        $header_template,
        'grid');

    if ($dbi->checkUserPermission('edit')) {
        $content .= '<div class="buttons">';
        $content .= createButton ('New Authority Record',
            'nmv_edit_authority_record_institution?ID_institution='.$institution_id,'icon add');
        $content .= '</div>';
    }
    $content .= '</div><br>';

} else {
    $institution_name = 'Error: Unknown Institution';
    $content = buildElement('p','Error: Institution not found. Maybe it has been deleted from the database?');
}
$content .= '<div class="buttons">';
if ($dbi->checkUserPermission('edit'))
    $content .= createButton ('Edit Institution','nmv_edit_institution?ID_institution='.$institution_id,'icon edit');
if ($dbi->checkUserPermission('admin'))
    $content .= createButton(L_DELETE,'nmv_remove_institution?ID_institution='.$institution_id,'icon delete');
$content .= '</div>';

$content .= '<div class="buttons">';
$content .= createButton("Experiments",'nmv_result_experiments?ID_institution='.$institution_id,'icon report-paper');
$content .= createButton("Victims (Experiment)",'nmv_result_victims_variable?exp_institution='.$institution_id,'icon report-paper');
$content .= createButton("Victims (Hospitalisation)",'nmv_result_victims_variable?hospitalisation_institution='.$institution_id,'icon report-paper');
$content .= createButton("Victims (Brain Report)",'nmv_result_victims_variable?brain_report_institution='.$institution_id,'icon report-paper');
$content .= createButton("Victims (Tissue)",'nmv_result_victims_variable?tissue_institution='.$institution_id,'icon report-paper');
$content .= createButton("Victims (Death Institution)",'nmv_result_victims_variable?ID_death_institution='.$institution_id,'icon report-paper');
$content .= createButton("Victims (Imprisonment Institution)",'nmv_result_victims_variable?ID_imprisonment_institution='.$institution_id,'icon report-paper');
if(in_array($institution_type, array('Archive', 'Museum', 'Library', 'Academic Institution'))){
  $content .=createButton("Sources (Archives ...)",'nmv_result_source?ID_institution='.$institution_id,'icon report-paper');
}
$content .= '</div>';
$content .= createBackLink ("List of Institutions",'nmv_list_institutions');

$layout
	->set('title','Institution: '.$institution_name)
	->set('content',$content)
	->cast();
