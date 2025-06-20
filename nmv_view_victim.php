<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$dbi->setUserVar ('ID_victim',getUrlParameter('ID_victim'),NULL);
$victim_id = (int) getUrlParameter('ID_victim',0);
$victim_role = '';
$victim_id_old_profile = NULL;
$title = '';

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Victims','nmv_list_victims');

// query: get victim data
$querystring = "
    SELECT v.first_names, v.surname, v.openUid, v.uid, v.entry_status, v.potential_old_profile, v.display_marker,
           CONCAT(IFNULL(v.birth_day , '-'), '.', IFNULL(v.birth_month , '-'), '.', IFNULL(v.birth_year, '-')) birth, v.twin,
           v.birth_place, bc.country AS birth_country, v.death_place, dc.country AS death_country,
           CONCAT(IFNULL(di.institution_name, ''), ' - ', IFNULL(di.location, ''), ' - ', IFNULL(v.death_institution, '')) AS death_institution,
           CONCAT(IFNULL(v.death_day , '-'), '.', IFNULL(v.death_month , '-'), '.', IFNULL(v.death_year, '-')) death,
           v.death_year, v.cause_of_death, v.gender, m.marital_family_status,
           ed.education AS education, r.religion,
           n.nationality, e.ethnic_group, v.ID_dataset_origin,
           p.occupation, v.occupation_details, v.notes, v.internal_notes,
           v.residence_after_1945_country, v.residence_after_1945_place,
           v.occupation_after_1945, n45.nationality AS nationality_after_1945,
           v.consequential_injuries, IFNULL(v.compensation, 'not specified') AS compensation, v.compensation_details,
           v.notes_after_1945, v.mpg_project, v.arrest_prehistory, v.arrest_location, ac.country AS arrest_country, v.arrest_history,
           IF(v.stolperstein_exists, 'Yes', '-') AS stolperstein_exists, 
           IF(v.photo_exists, 'Yes', '-') AS photo_exists, v.notes_photo, v.was_prisoner_assistant,
           v.evaluation_list, evs.status AS evaluation_status, v.status_due_to, v.status_notes, v.mpg_project AS mpg_project, d.work_group AS workgroup,
           v.ID_new_profile, v.visibility 
    FROM nmv__victim v
    LEFT JOIN nmv__marital_family_status m ON (m.ID_marital_family_status = v.ID_marital_family_status )
    LEFT JOIN nmv__education ed ON (ed.ID_education = v.ID_education)
    LEFT JOIN nmv__religion r ON (r.ID_religion = v.ID_religion)
    LEFT JOIN nmv__nationality n ON (n.ID_nationality = v.ID_nationality_1938)
    LEFT JOIN nmv__nationality n45 ON (n45.ID_nationality = v.ID_nationality_after_1945)
    LEFT JOIN nmv__ethnic_group e ON (e.ID_ethnic_group = v.ID_ethnic_group)
    LEFT JOIN nmv__occupation p ON (p.ID_occupation = v.ID_occupation)
    LEFT JOIN nmv__country bc ON (bc.ID_country = v.ID_birth_country)
    LEFT JOIN nmv__country dc ON (dc.ID_country = v.ID_death_country)
    LEFT JOIN nmv__country ac ON (ac.ID_country = v.ID_arrest_country)
    LEFT JOIN nmv__institution di ON (di.ID_institution = v.ID_death_institution)
    LEFT JOIN nmv__victim_source vs ON vs.ID_victim = v.ID_victim
    LEFT JOIN nmv__victim_literature vl ON vl.ID_victim = v.ID_victim
    LEFT JOIN nmv__med_history_hosp h ON h.ID_victim = v.ID_victim
    LEFT JOIN nmv__med_history_brain b ON b.ID_victim = v.ID_victim
    LEFT JOIN nmv__victim_evaluation_status evs ON evs.ID_evaluation_status = v.ID_evaluation_status
    LEFT JOIN nmv__dataset_origin d ON d.ID_dataset_origin = v.ID_dataset_origin
    WHERE v.ID_victim = ?";

$result = null;
if ($stmt = $dbi->connection->prepare($querystring)) {
    if ( $stmt->bind_param('i', $victim_id) ) {
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

if ($victim = $result->fetch_object()) {
    $victim_name = $victim->first_names.' '.$victim->surname;
    $victim_id_new_profile = $victim->ID_new_profile;
    $victim_birth = $victim->birth.
        ($victim->birth_place ? ' in '.$victim->birth_place : '').
        ($victim->birth_country ? ' in '.$victim->birth_country : '').
        ($victim->twin ? ' as a twin' : '');
    $victim_death = $victim->death.
        ($victim->death_place?' in '.$victim->death_place:'').
        ($victim->death_country?' in '.$victim->death_country :'').
        ($victim->{"cause_of_death"}?', cause of death: '.$victim->{"cause_of_death"}:'');
    $content = buildElement('h3', 'Personal Data');
    if($victim->mpg_project == -1) {
        $content .= buildElement('h3', 'mpgcolor', "Data from MPG Victims Research Project - workgroup $victim->workgroup");
    }
    if($victim->entry_status != NULL) {
        $content .= buildElement('p', "Status Data Transfer: " . $victim->entry_status);
    }
    if($victim_id_new_profile != NULL) {
        if($victim->ID_dataset_origin == 12) {
            $content .= buildElement('h3', 'mpgcolor', "This profile was created based on notes provided by Patricia Heberer Rice (USHMM) drawn from patient files of the Heil- und Pflegeanstalt Kaufbeuren-Irsee.
                        <br>It reflects the state of research prior to the beginning of the Brain Research Project (before 2017).");
        }
        else {
            $content .= buildElement('h3', 'mpgcolor', "This profile represents the state of research prior to the start of the Brain Research Project (before 2017) - workgroup Oxford");
        }
    }
        $content .= buildElement('table','grid',
        buildDataSheetRow('ID',                      $victim_id).
        buildDataSheetRow('openUid',                $victim->openUid).
        buildDataSheetRow('Uid',                $victim->uid).
        buildDataSheetRow('Name',                   $victim_name).
        buildDataSheetRow('Gender',                 $victim->gender).
        buildDataSheetRow('Birth DMY',                  $victim_birth).
        buildDataSheetRow('Death DMY',                  $victim_death).
        buildDataSheetRow('Death Institution',     $victim->death_institution).
        buildDataSheetRow('Marital familiy status',
            $victim->marital_family_status).
        buildDataSheetRow('Highest education level',$victim->education).
        buildDataSheetRow('Occupation',
            $victim->occupation.
            ($victim->occupation_details ?' ('.$victim->occupation_details.')':'')).
        buildDataSheetRow('Religion',               $victim->religion).
        buildDataSheetRow('Nationality (1938)',     $victim->nationality).
        buildDataSheetRow('Ascribed Ethnic Group',           $victim->ethnic_group).
        buildDataSheetRow('Notes',$victim->notes).
        buildDataSheetRow('Internal Notes', $victim->internal_notes).
        buildDataSheetRow('Visibility on Website',    $victim->visibility).
        buildDataSheetRow('Markers for special display features on Website', $victim->display_marker));
    if ($victim->stolperstein_exists == 'Yes') {
        $content .= buildElement('table', 'grid',
            buildDataSheetRow('Stolperstein exists',  $victim->stolperstein_exists));
    }
    if ($victim->photo_exists == 'Yes') {
        $content .= buildElement('table','grid',
            buildDataSheetRow('Photo exists',           $victim->photo_exists).
            buildDataSheetRow('Notes about photo',      $victim->notes_photo));
    }
    $content .= '<br>'.buildElement('table','grid',
            buildDataSheetRow('Arrest prehistory',      $victim->arrest_prehistory).
            buildDataSheetRow('Arrest location',        $victim->arrest_location).
            buildDataSheetRow('Arrest country',      $victim->arrest_country).
            buildDataSheetRow('Arrest history',      $victim->arrest_history));
        //complete db d 1
    if (!($dbi->checkUserPermission('mpg'))) :
        $content .= '<br>'.buildElement('h3', 'Post 1945');
        if(!($victim->mpg_project == -1) && ($victim->death_year == NULL || !($victim->death_year < 1946)) && ($victim->cause_of_death != 'T4 euthanasia' )):
            $content .= buildElement('table','grid',
                buildDataSheetRow('Country and place',
                    $victim->residence_after_1945_country . ' / ' .
                    $victim->residence_after_1945_place).
                buildDataSheetRow('Occupation',             $victim->occupation_after_1945).
                buildDataSheetRow('Nationality',            $victim->nationality_after_1945).
                buildDataSheetRow('Consequential injuries', $victim->consequential_injuries).
                buildDataSheetRow('Notes on life after 1945', $victim->notes_after_1945)
            );
        endif;
        $content .= buildElement('table', 'grid',
            buildDataSheetRow('Compensation',           $victim->compensation).
            buildDataSheetRow('Compensation details',   $victim->compensation_details)
        );
    endif;
    $content .= '<br>'.buildElement('h3', 'Evaluation');
    $content .= buildElement('table', 'grid',
        buildDataSheetRow('Evaluation Status',              $victim->evaluation_status).
        buildDataSheetRow('Status due to',                  $victim->status_due_to).
        buildDataSheetRow('Status notes',                   $victim->status_notes).
        buildDataSheetRow('Evaluation List',                $victim->evaluation_list)
    );

    $content .= '<div class="indent">';
    $content .= '<br>'.buildElement('h3', 'Other Names');
    // query: get other names
    $querystring = "
    SELECT vn.ID_name ID_name,
        vn.surname surname, vn.first_names first_names,
        vnt.nametype
    FROM nmv__victim_name vn
    LEFT JOIN nmv__victim_nametype vnt ON vnt.ID_nametype = vn.ID_nametype
    WHERE vn.ID_victim = $victim_id
    ORDER BY nametype, surname, first_names";

    $options = '';
    $row_template = ['{surname}', '{first_names}', '{nametype}'];
    $header_template = ['Surname', 'First Names', 'Name Type'];

    if ($dbi->checkUserPermission('edit') || $dbi->checkUserPermission('admin')) {
        if ($dbi->checkUserPermission('edit')) {
            $options .= createSmallButton(L_EDIT,'nmv_edit_victim_other_names?ID_name={ID_name}','icon edit');
        }
        if ($dbi->checkUserPermission('admin')) {
            $options .= createSmallButton(L_DELETE,'nmv_remove_victim_other_names?ID_name={ID_name}','icon delete');
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
        $content .= createButton ('New name',
            'nmv_edit_victim_other_names?ID_victim='.$victim_id,'icon add');
        $content .= '</div>';
    }

    $content .= '<br>'.buildElement('h3', 'Imprisonment');
    // query: get prison numbers
    $querystring = "
    SELECT i.ID_imprisonment, i.ID_victim, i.number, ins.institution_name AS institution, i.location,
        GROUP_CONCAT(c.classification SEPARATOR ', <br>') AS classification, CONCAT(IFNULL(i.start_day, '-'), '.', IFNULL(i.start_month, '-'), '.', IFNULL(i.start_year, '-')) AS start_date
    FROM nmv__imprisonment i
    LEFT JOIN nmv__imprisonment_classification ic    ON ic.ID_imprisonment = i.ID_imprisonment
    LEFT JOIN nmv__victim_classification c           ON c.ID_classification = ic.ID_classification
    LEFT JOIN nmv__institution ins ON ins.ID_institution = i.ID_institution
    WHERE ID_victim = $victim_id
    GROUP BY i.ID_imprisonment
    ORDER BY start_year, start_month, start_day";

    $options = '';
    $row_template = ['{number}', '{institution}', '{location}', '{classification}', '{start_date}'];
    $header_template = ['(Prisoner) Number', 'Institution', 'Location', 'Classifications', 'Date (DMY)'];

    if ($dbi->checkUserPermission('edit') || $dbi->checkUserPermission('admin')) {
        if ($dbi->checkUserPermission('edit')) {
            $options .= createSmallButton(L_EDIT,'nmv_edit_victim_imprisonment?ID_imprisonment={ID_imprisonment}&ID_victim={ID_victim}','icon edit');
        }
        if ($dbi->checkUserPermission('admin')) {
            $options .= createSmallButton(L_DELETE,'nmv_remove_victim_imprisonment?ID_imprisonment={ID_imprisonment}','icon delete');
        }
        if ($dbi->checkUserPermission('edit')) {
            $options .= createSmallButton('edit classifications','nmv_edit_imprisonment_classification?ID_imprisonment={ID_imprisonment}&ID_victim={ID_victim}','icon edit');
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
        $content .= createButton ('New imprisonment (prison number)',
            'nmv_edit_victim_imprisonment?ID_victim='.$victim_id,'icon add');
        $content .= '</div>';
    }
    //get linked new profile if existent
    $querystring = "SELECT v1.ID_victim AS ID_old_profile
                    FROM nmv__victim v1
                    WHERE EXISTS (SELECT v2.ID_victim
                                  FROM nmv__victim v2
                                  WHERE v1.ID_victim = v2.ID_victim
                                  AND v2.ID_new_profile = $victim_id)";
    $result_old_profile = $dbi->connection->query($querystring)->fetch_object();
    if($result_old_profile != NULL) $victim_id_old_profile = $result_old_profile->ID_old_profile;
    } else {
    $victim_name = 'Error: unknown victim';
    $content = buildElement('p','Error: Victim not found. Maybe it has been deleted from the database?');
}

$content .= '</div><div class="buttons">';
if ($dbi->checkUserPermission('edit'))
    $content .= createButton ('Edit personal data','nmv_edit_victim?ID_victim='.$victim_id,'icon edit');
if ($dbi->checkUserPermission('admin'))
    $content .= createButton('Delete complete entry','nmv_remove_victim?ID_victim='.$victim_id,'icon delete');
$content .= '<br>';
$content .= createButton("Medical History",'nmv_list_med_hist?ID_victim='.$victim_id,'icon report-paper');
//complete db d 2
if (!($dbi->checkUserPermission('mpg'))) :
    $content .= createButton("Biomedical Research",'nmv_list_victim_experiment?ID_victim='.$victim_id,'icon report-paper');
endif;
$content .= createButton("Literature and Sources", 'nmv_list_victim_literature_and_sources?ID_victim='.$victim_id, 'icon report-paper');
if($victim_id_new_profile != NULL)
    $content .= '<br>' . createButton("Switch to MPG Project Profile", 'nmv_view_victim.php?ID_victim='.$victim_id_new_profile, 'icon report-paper mpgcolor');
if($victim_id_old_profile != NULL)
    $content .= '<br>' . createButton("Switch to Pre MPG Project Profile", 'nmv_view_victim.php?ID_victim='.$victim_id_old_profile, 'icon report-paper mpgcolor');
$content .= '</div>';
$content .= createBackLink ("Back to Previous Page");
$title .= 'Victim: ' . $victim_name . '<br>';
if($victim->was_prisoner_assistant =='prisoner assistant only'){
    $title .= '<span class="red">Prisoner Assistant:</span> ' . $victim_name;
    $content = 'Prisoner Assistants were forced to participate in the conduction of unethical biomedical research
              <br>Please find information about involvement in experiments in '.createButton("Biomedical Research",'nmv_list_victim_experiment?ID_victim='.$victim_id,'icon report-paper') . $content;
} else if($victim->was_prisoner_assistant == 'prisoner assistant AND victim') {
    $title .= 'Victim and <span class="red">Prisoner Assistant: </span>' .$victim_name;
    $content = $victim_name . ' was victim of experiments and was also forced to participate in the conduction of unethical biomedical research' . $content;
}

//complete db
if(($dbi->checkUserPermission('mpg')) AND ($victim->mpg_project != -1 AND $victim->potential_old_profile != -1)) {
    $title = 'Victim not found';
    $content = '';
}

$layout
    ->set('title', $title)
    ->set('content',$content)
    ->cast();
