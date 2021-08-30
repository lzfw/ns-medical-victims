<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$dbi->setUserVar ('ID_experiment',getUrlParameter('ID_experiment'),NULL);
$experiment_id = (int) getUrlParameter('ID_experiment',0);

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Biomedical Research','nmv_list_experiments');

// query: get experiment data
$querystring = '
    SELECT e.ID_experiment, e.experiment_title AS experiment_title, c.english AS classification, e.funding AS funding,
        e.field_of_interest AS field_of_interest, e.objective AS objective,
        e.number_victims_remark AS number_victims_remark, e.notes AS notes,
        e.number_victims_estimate AS number_victims_estimate,
        e.number_fatalities_estimate AS number_fatalities_estimate,
        e.confirmed_experiment AS confirmed_experiment,
        e. location_details AS location_details,
        CONCAT_WS(\'-\', e.start_year, e.start_month, e.start_day) AS start,
        CONCAT_WS(\'-\', e.end_year, e.end_month, e.end_day) AS end,
        e.notes_location AS notes_location,
    LEFT(concat(IFNULL(LEFT(i.institution_name, 60), \'#\'),\' - \',IFNULL(LEFT(i.location,40), \'#\'),\' - \',IFNULL(co.english, \'#\')),100) institution
    FROM nmv__experiment e
    LEFT JOIN nmv__experiment_classification c ON c.ID_exp_classification = e.classification
    LEFT JOIN nmv__institution i ON i.ID_institution = e.ID_institution
    LEFT JOIN nmv__country co ON co.ID_country = i.ID_country
    WHERE ID_experiment = ?';

$result = null;
if ($stmt = $dbi->connection->prepare($querystring)) {
    if ( $stmt->bind_param('i', $experiment_id) ) {
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

if ($experiment = $result->fetch_object()) {
    $confirmed = $experiment->confirmed_experiment ? ' (confirmed biomedical research)' : '';
    $experiment_name = $experiment->experiment_title . $confirmed;

    $content = buildElement('table','grid',
        buildDataSheetRow('ID experiment',            $experiment->ID_experiment).
        buildDataSheetRow('Title',                    $experiment->experiment_title . $confirmed).
        buildDataSheetRow('Classification',           $experiment->classification).
        buildDataSheetRow('Institution',              $experiment->institution).
        buildDataSheetRow('Location Details',         $experiment->location_details).
        buildDataSheetRow('Notes on location',        $experiment->notes_location).
        buildDataSheetRow('Funding',                  $experiment->funding).
        buildDataSheetRow('Duration',                 $experiment->start . ' - ' . $experiment->end).
        buildDataSheetRow('Field of interest',        $experiment->field_of_interest).
        buildDataSheetRow('Objective',                $experiment->objective).
        buildDataSheetRow('Number of victims (estimate)',    $experiment->number_victims_estimate).
        buildDataSheetRow('Number of fatalities (estimate)', $experiment->number_fatalities_estimate).
        buildDataSheetRow('Remark about victim numbers',     $experiment->number_victims_remark).
        buildDataSheetRow('Notes',                    $experiment->notes)
    );
} else {
    $experiment_name = 'Error: unknown biomedical research';
    $content = buildElement('p','Error: Biomedical research not found. Maybe it has been deleted from the database?');
}

$content .= '<div class="buttons">';
if ($dbi->checkUserPermission('edit'))
    $content .= createButton ('Edit Biomedical Research','nmv_edit_experiment?ID_experiment='.$experiment_id,'icon edit');
if ($dbi->checkUserPermission('admin'))
    $content .= createButton(L_DELETE,'nmv_remove_experiment?ID_experiment='.$experiment_id,'icon delete');
$content .= createButton("Victims",'nmv_list_victim_experiment?ID_experiment='.$experiment_id,'icon report-paper');
$content .= createButton("Perpetrators",'nmv_list_perpetrator_experiment?ID_experiment='.$experiment_id,'icon report-paper');
$content .= createButton("Literature",'nmv_list_experiment_literature?ID_experiment='.$experiment_id,'icon report-paper');
$content .= createButton("Sources",'nmv_list_experiment_source?ID_experiment='.$experiment_id,'icon report-paper');
$content .= '</div>';

$content .= createBackLink ("List of Biomedical Research",'nmv_list_experiments');


$layout
	->set('title','Biomedical Research: '.$experiment_name)
	->set('content',$content)
	->cast();
