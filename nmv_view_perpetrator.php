<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$dbi->setUserVar ('ID_perpetrator',getUrlParameter('ID_perpetrator'),NULL);
$perpetrator_id = (int) getUrlParameter('ID_perpetrator',0);

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Perpetrators','nmv_list_perpetrators');

// query: get perpetrator data
$querystring = '
    SELECT first_names, surname, titles,
           CONCAT_WS(\'-\', birth_year, birth_month, birth_day) birth,
           birth_place, birth_country, death_place, death_country,
           CONCAT_WS(\'-\', death_year, death_month, death_day) death,
           gender, r.english as religion,
           n.english as nationality, c.english as classification, occupation,
           career_history, place_of_qualification_1, year_of_qualification_1, type_of_qualification_1,
           title_of_dissertation_1, place_of_qualification_2, year_of_qualification_2, type_of_qualification_2,
           title_of_dissertation_2, nsdap_member, nsdap_since_when,
           ss_member, ss_since_when, sa_member, sa_since_when,
           other_nsdap_organisations_member, details_all_memberships,
           career_after_1945, prosecution, prison_time, notes
    FROM nmv__perpetrator p
    LEFT JOIN nmv__religion r ON (r.ID_religion = p.religion)
    LEFT JOIN nmv__nationality n ON (n.ID_nationality = p.nationality_1938)
    LEFT JOIN nmv__perpetrator_classification c ON (c.ID_perp_class = p.ID_perp_class)
    WHERE ID_perpetrator = ?';

$result = null;
if ($stmt = $dbi->connection->prepare($querystring)) {
    if ( $stmt->bind_param('i', $perpetrator_id) ) {
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

function formatMembership($is_member, $since) {
    return ( $is_member ? 'yes' : 'no / unknown' ) .
        ($since ? ', since ' . $since : '');
}

if ($perpetrator = $result->fetch_object()) {
    $perpetrator_name = $perpetrator->first_names.' '.$perpetrator->surname.' ('.$perpetrator->titles.')';
    $perpetrator_birth = $perpetrator->birth.
        ($perpetrator->birth_place ? ' in '.$perpetrator->birth_place : '').
        ($perpetrator->birth_country ? ' ('.$perpetrator->birth_country . ')' : '');
    $perpetrator_death = $perpetrator->death.
        ($perpetrator->death_place ?' in '.$perpetrator->death_place:'').
        ($perpetrator->death_country ? ' ('.$perpetrator->death_country.')':'');
    $nsdap_member = formatMembership(
        $perpetrator->nsdap_member, $perpetrator->nsdap_since_when);
    $ss_member = formatMembership(
        $perpetrator->ss_member, $perpetrator->ss_since_when);
    $sa_member = formatMembership(
        $perpetrator->sa_member, $perpetrator->sa_since_when);

    $content = buildElement('table','grid',
        buildDataSheetRow('Perpetrator ID',         $perpetrator_id).
        buildDataSheetRow('Name',                   $perpetrator_name).
        buildDataSheetRow('Gender',                 $perpetrator->gender).
        buildDataSheetRow('Birth (d/m/y)',          $perpetrator_birth).
        buildDataSheetRow('Death (d/m/y)',          $perpetrator_death).
        buildDataSheetRow('Religion',               $perpetrator->religion).
        buildDataSheetRow('Nationality (1938)',     $perpetrator->nationality).
        buildDataSheetRow('Occupation',             $perpetrator->occupation).
        buildDataSheetRow('Classification',         $perpetrator->classification).
        buildDataSheetRow('Career history',         $perpetrator->career_history).
        buildDataSheetRow('Type of qualification 1',
            $perpetrator->type_of_qualification_1).
        buildDataSheetRow('Place and year of qualification 1',
            $perpetrator->place_of_qualification_1 . ' ' .
            $perpetrator->year_of_qualification_1).
        buildDataSheetRow('Title of dissertation 1',
            $perpetrator->title_of_dissertation_1).
        buildDataSheetRow('Type of qualification 2',
            $perpetrator->type_of_qualification_2).
        buildDataSheetRow('Place and year of qualification 2',
            $perpetrator->place_of_qualification_2 . ' ' .
            $perpetrator->year_of_qualification_2).
        buildDataSheetRow('Title of dissertation 2',
            $perpetrator->title_of_dissertation_2).
        buildDataSheetRow('NSDAP member',           $nsdap_member).
        buildDataSheetRow('SS member',              $ss_member).
        buildDataSheetRow('SA member',              $sa_member).
        buildDataSheetRow('Other NSDAP org. memebership',
            $perpetrator->other_nsdap_organisations_member ? 'yes' : 'no / unknown').
        buildDataSheetRow('Membership details',
            $perpetrator->details_all_memberships).
        buildDataSheetRow('Career after 1945',      $perpetrator->career_after_1945).
        buildDataSheetRow('Prosecution',            $perpetrator->prosecution).
        buildDataSheetRow('Prison time',            $perpetrator->prison_time).
        buildDataSheetRow('Notes',                  $perpetrator->notes)
    );
} else {
    $perpetrator_name = 'Error: unknown perpetrator';
    $content = buildElement('p','Error: Perpetrator not found. Maybe it has been deleted from the database?');
}

$content .= '<div class="buttons">';
if ($dbi->checkUserPermission('edit'))
    $content .= createButton ('Edit Perpetrator','nmv_edit_perpetrator?ID_perpetrator='.$perpetrator_id,'icon edit');
if ($dbi->checkUserPermission('admin'))
    $content .= createButton(L_DELETE,'nmv_remove_perpetrator?ID_perpetrator='.$perpetrator_id,'icon delete');
//complete db d
if (!($dbi->checkUserPermission('mpg'))) :
  $content .= createButton("Biomedical Research",'nmv_list_perpetrator_experiment?ID_perpetrator='.$perpetrator_id,'icon report-paper');
endif;
$content .= createButton("Literature",'nmv_list_perpetrator_literature?ID_perpetrator='.$perpetrator_id,'icon report-paper');
$content .= createButton("Sources",'nmv_list_perpetrator_source?ID_perpetrator='.$perpetrator_id,'icon report-paper');
$content .= '</div>';

$content .= createBackLink ("List of Perpetrators",'nmv_list_perpetrators');


$layout
	->set('title','Perpetrator: '.$perpetrator_name)
	->set('content',$content)
	->cast();
