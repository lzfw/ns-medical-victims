<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$dbi->setUserVar ('ID_victim',getUrlParameter('ID_victim'),NULL);
$victim_id = (int) getUrlParameter('ID_victim',0);
$victim_role = '';

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Victims','nmv_list_victims');

// query: get victim data
$querystring = "
    SELECT v.first_names, v.surname,
           CONCAT(IFNULL(v.birth_day , '-'), '.', IFNULL(v.birth_month , '-'), '.', IFNULL(v.birth_year, '-')) birth, v.twin,
           v.birth_place, bc.english as birth_country, v.death_place, dc.english as death_country,
           CONCAT(IFNULL(v.death_day , '-'), '.', IFNULL(v.death_month , '-'), '.', IFNULL(v.death_year, '-')) death,
           v.cause_of_death, gender, m.english as marital_family_status,
           ed.english as education, r.english as religion,
           n.english as nationality, e.english as ethnic_group,
           p.english as occupation, v.occupation_details, v.notes,
           v.residence_after_1945_country, v.residence_after_1945_place,
           v.occupation_after_1945, n45.english nationality_after_1945,
           v.consequential_injuries, IFNULL(v.compensation, 'not specified') AS compensation, v.compensation_details,
           v.notes_after_1945, v.mpg_project, v.arrest_prehistory, v.arrest_location, ac.english as arrest_country, v.arrest_history,
           IF(v.photo_exists, 'Yes', '-') AS photo_exists, v.notes_photo, v.was_prisoner_assistant
    FROM nmv__victim v
    LEFT JOIN nmv__marital_family_status m ON (m.ID_marital_family_status = v.ID_marital_family_status )
    LEFT JOIN nmv__education ed ON (ed.ID_education = v.ID_education)
    LEFT JOIN nmv__religion r ON (r.ID_religion = v.religion)
    LEFT JOIN nmv__nationality n ON (n.ID_nationality = v.nationality_1938)
    LEFT JOIN nmv__nationality n45 ON (n45.ID_nationality = v.nationality_after_1945)
    LEFT JOIN nmv__ethnicgroup e ON (e.ID_ethnicgroup = v.ethnic_group)
    LEFT JOIN nmv__occupation p ON (p.ID_occupation = v.occupation)
    LEFT JOIN nmv__country bc ON (bc.ID_country = v.ID_birth_country)
    LEFT JOIN nmv__country dc ON (dc.ID_country = v.ID_death_country)
    LEFT JOIN nmv__country ac ON (ac.ID_country = v.ID_arrest_country)
    LEFT JOIN nmv__victim_source vs ON vs.ID_victim = v.ID_victim
    LEFT JOIN nmv__victim_literature vl ON vl.ID_victim = v.ID_victim
    LEFT JOIN nmv__med_history_hosp h ON h.ID_victim = v.ID_victim
    LEFT JOIN nmv__med_history_brain b ON b.ID_victim = v.ID_victim
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
    $victim_birth = $victim->birth.
        ($victim->birth_place ? ' in '.$victim->birth_place : '').
        ($victim->birth_country ? ' in '.$victim->birth_country : '').
        ($victim->twin ? ' as a twin' : '');
    $victim_death = $victim->death.
        ($victim->death_place?' in '.$victim->death_place:'').
        ($victim->death_country?' in '.$victim->death_country :'').
        ($victim->{"cause_of_death"}?', cause of death: '.$victim->{"cause_of_death"}:'');
    $content = buildElement('h3', 'Personal Data');
    $content .= buildElement('table','grid',
        buildDataSheetRow('ID',              $victim_id).
        buildDataSheetRow('Name',                   $victim_name).
        buildDataSheetRow('MPG project',            $victim->mpg_project ? 'Yes' : '-').
        buildDataSheetRow('Gender',                 $victim->gender).
        buildDataSheetRow('Birth DMY',                  $victim_birth).
        buildDataSheetRow('Death DMY',                  $victim_death).
        buildDataSheetRow('Marital familiy status',
            $victim->marital_family_status).
        buildDataSheetRow('Highest education level',$victim->education).
        buildDataSheetRow('Religion',               $victim->religion).
        buildDataSheetRow('Nationality (1938)',     $victim->nationality).
        buildDataSheetRow('Ethnic group',           $victim->ethnic_group).
        buildDataSheetRow('Occupation',
            $victim->occupation.
            ($victim->occupation_details ?' ('.$victim->occupation_details.')':'')).
        buildDataSheetRow('Notes',$victim->notes));

        if ($victim->photo_exists == 'Yes') {
        $content .= buildElement('table','grid',
        buildDataSheetRow('Photo exists',           $victim->photo_exists).
        buildDataSheetRow('Notes about photo',      $victim->notes_photo));
      }
        //complete db
        if(!($dbi->checkUserPermission('mpg'))):
          $content .= '<br>'.buildElement('table','grid',
          buildDataSheetRow('Arrest prehistory',      $victim->arrest_prehistory).
          buildDataSheetRow('Arrest location',        $victim->arrest_location).
          buildDataSheetRow('Arrest country',      $victim->arrest_country).
          buildDataSheetRow('Arrest history',      $victim->arrest_history));
        endif;
    //complete db d 1
    if (!($dbi->checkUserPermission('mpg'))) :
      $content .= '<br>'.buildElement('h3', 'Life after 1945');
      $content .= buildElement('table','grid',
          buildDataSheetRow('Country and place',
              $victim->residence_after_1945_country . ' / ' .
              $victim->residence_after_1945_place).
          buildDataSheetRow('Occupation',             $victim->occupation_after_1945).
          buildDataSheetRow('Nationality',            $victim->nationality_after_1945).
          buildDataSheetRow('Consequential injuries', $victim->consequential_injuries).
          buildDataSheetRow('Compensation',           $victim->compensation).
          buildDataSheetRow('Compensation details',   $victim->compensation_details).
          buildDataSheetRow('Notes on life after 1945', $victim->notes_after_1945)
      );
    endif;

    $content .= '<div class="indent">';
    $content .= '<br>'.buildElement('h3', 'Other Names');
    // query: get other names
    $querystring = "
    SELECT vn.ID_name ID_name,
        vn.victim_name victim_name, vn.victim_first_names victim_first_names,
        vnt.english nametype
    FROM nmv__victim_name vn
    LEFT JOIN nmv__victim_nametype vnt ON vnt.ID_nametype = vn.nametype
    WHERE vn.ID_victim = $victim_id
    ORDER BY nametype, victim_name, victim_first_names";

    $options = '';
    $row_template = ['{victim_name}', '{victim_first_names}', '{nametype}'];
    $header_template = ['Name', 'First Names', 'Name Type'];

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
    SELECT ID_imprisoniation, ID_victim, number, location,
        pc.english classification, CONCAT(IFNULL(start_day, '-'), '.', IFNULL(start_month, '-'), '.', IFNULL(start_year, '-')) AS start_date
    FROM nmv__imprisoniation i
    LEFT JOIN nmv__victim_classification pc ON pc.ID_classification = i.ID_classification
    WHERE ID_victim = $victim_id
    ORDER BY start_year, start_month, start_day";

    $options = '';
    $row_template = ['{number}', '{location}', '{classification}', '{start_date}'];
    $header_template = ['Number', 'Location', 'Classification', 'Date (DMY)'];

    if ($dbi->checkUserPermission('edit') || $dbi->checkUserPermission('admin')) {
    	if ($dbi->checkUserPermission('edit')) {
    			$options .= createSmallButton(L_EDIT,'nmv_edit_victim_imprisoniation?ID_imprisoniation={ID_imprisoniation}','icon edit');
    	}
    	if ($dbi->checkUserPermission('admin')) {
    			$options .= createSmallButton(L_DELETE,'nmv_remove_victim_imprisoniation?ID_imprisoniation={ID_imprisoniation}','icon delete');
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
    	    'nmv_edit_victim_imprisoniation?ID_victim='.$victim_id,'icon add');
    	$content .= '</div>';
    }

    $content .= '<br>'.buildElement('h3', 'Evaluation');

    //query: get evaluation data
    $querystring = '
    SELECT ID_evaluation, ID_victim, confirmed_victim, confirmed_due_to,
           es.english "evaluation_status", status_due_to, status_notes,
           pending_notes, pending_due_to, evaluation_list
        FROM nmv__evaluation e
        LEFT JOIN nmv__victim_evaluation_status es
            ON (e.evaluation_status = es.ID_status)
        WHERE ID_victim = ?
        ORDER BY ID_evaluation
        LIMIT 5';

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

    if ($evaluation = $result->fetch_object()) {
        $content .= buildElement('table','grid',
            buildDataSheetRow('Status', $evaluation->evaluation_status).
            buildDataSheetRow('Status due to',       $evaluation->status_due_to).
            buildDataSheetRow('Status Notes',  $evaluation->status_notes).
            ($evaluation->pending_due_to ? buildDataSheetRow('Pending due to',  $evaluation->pending_due_to) : '').
            ($evaluation->pending_notes ? buildDataSheetRow('Pending Notes',  $evaluation->pending_notes) : '').
            buildDataSheetRow('Evaluation List',        $evaluation->evaluation_list)
        );
        if ($dbi->checkUserPermission('edit') || $dbi->checkUserPermission('admin')) {
            $content .= '<div class="buttons">';
        	if ($dbi->checkUserPermission('edit')) {
        			$content .= createSmallButton('Edit evaluation','nmv_edit_victim_evaluation?ID_evaluation=' . $evaluation->ID_evaluation,'icon edit');
        	}
        	if ($dbi->checkUserPermission('admin')) {
        			$content .= createSmallButton('Delete evaluation','nmv_remove_victim_evaluation?ID_evaluation=' . $evaluation->ID_evaluation,'icon delete');
        	}
        	$content .= '</div>';
        }
    } else {
        if ($dbi->checkUserPermission('edit')) {
        	$content .= '<div class="buttons">';
        	$content .= createButton ('Add evaluation',
        	    'nmv_edit_victim_evaluation?ID_victim='.$victim_id,'icon add');
        	$content .= '</div>';
        }
    }
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
$content .= '</div>';

$content .= createBackLink ("List of Persons",'nmv_list_victims');

$title = 'Victim: ' . $victim_name;
if($victim->was_prisoner_assistant =='prisoner assistant only'){
  $title = '<span class="red">Prisoner Assistant:</span> ' . $victim_name;
  $content = 'Prisoner Assistants were forced to participate in the conduction of unethical biomedical research
              <br>Please find information about involvement in experiments in '.createButton("Biomedical Research",'nmv_list_victim_experiment?ID_victim='.$victim_id,'icon report-paper') . $content;
} else if($victim->was_prisoner_assistant == 'prisoner assistant AND victim') {
  $title = 'Victim and <span class="red">Prisoner Assistant: </span>' .$victim_name;
  $content = $victim_name . ' was victim of experiments and was also forced to participate in the conduction of unethical biomedical research' . $content;
}

$layout
	->set('title', $title)
	->set('content',$content)
	->cast();
