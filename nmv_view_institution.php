<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$dbi->setUserVar ('ID_institution',getUrlParameter('ID_institution'),NULL);
$institution_id = (int) getUrlParameter('ID_institution',0);

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Institutions','nmv_list_institutions');

// query: get institution data
$querystring = '
    SELECT ID_institution, institution_name, location, c.english AS country, t.english "type", notes
    FROM nmv__institution i
    LEFT JOIN nmv__institution_type t ON (t.ID_institution_type = i.`type`)
    LEFT JOIN nmv__country c ON c.ID_country = i.ID_country
    WHERE ID_institution = ?';

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

if ($institution = $result->fetch_object()) {
    $institution_name = $institution->institution_name;
    $content .= buildElement('table','grid',
        buildDataSheetRow('Institution ID',        $institution_id).
        buildDataSheetRow('Name',                  $institution->institution_name).
        buildDataSheetRow('Location',              $institution->location).
        buildDataSheetRow('Present Country',               $institution->country).
        buildDataSheetRow('Type',                  $institution->type).
        buildDataSheetRow('Notes',                 $institution->notes)
    );
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

$content .= createBackLink ("List of Institutions",'nmv_list_institutions');


$layout
	->set('title','Institution: '.$institution_name)
	->set('content',$content)
	->cast();
