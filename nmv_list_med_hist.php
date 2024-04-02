<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Victims','nmv_list_victims');

$victim_id = (int) getUrlParameter('ID_victim', 0);
$victim_name = 'Error: Missing victim.';
$content = '<br>';

if ($victim_id) {
    // query: get victim data
    $querystring = "
    SELECT CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_names, '')) victim_name
    FROM nmv__victim
    WHERE ID_victim = $victim_id";
    $query = $dbi->connection->query($querystring);
    $victim = $query->fetch_object();

    if ($victim) {
        $victim_name = $victim->victim_name;

        $dbi->addBreadcrumb ($victim_name,'nmv_view_victim?ID_victim='.$victim_id);

        // query: get hosp data
        $querystring = "
        SELECT  h.ID_med_history_hosp id, i.ID_institution id_institution, LEFT(h.notes, 30) notes, LEFT(h.diagnosis, 30) diagnosis,
                concat(
                  IFNULL(LEFT(i.institution_name, 150), '#'),' - ',
                  IFNULL(LEFT(i.location,40), '#'),' - ',
                  IFNULL(c.country, '#')) institution,
                h.institution other_institution, IF(h.hosp_has_photo, 'yes', '-') AS photo,
                CONCAT_WS('.', IFNULL(h.date_entry_day, '-'), IFNULL(h.date_entry_month, '-'), IFNULL(h.date_entry_year, '-')) entry_date,
                CONCAT_WS('.', IFNULL(h.date_exit_day, '-'), IFNULL(h.date_exit_month, '-'), IFNULL(h.date_exit_year, '-')) exit_date
        FROM nmv__med_history_hosp h
        LEFT JOIN nmv__institution i ON i.ID_institution = h.ID_institution
        LEFT JOIN nmv__country c ON c.ID_country = i.ID_country
        WHERE ID_victim = $victim_id
        ORDER BY h.date_entry_year, h.date_entry_month, h.date_entry_day";
        $query = $dbi->connection->query($querystring);

        $content .= '<h3>Hospitalization</h3>';
        $content .= '<table class="grid">';
        $content .= '<tr><th>Institution</th><th>Entry Date<br>(D.M.Y)</th><th>Exit Date<br>(D.M.Y)</th><th>notes (trunc.)</th><th>diagnosis (trunc.)</th><th>ID</th><th>Photo<th>Options</th>';
        $content .= '</tr>';
        while ($entry = $query->fetch_object()) {
        	$content .= '<tr>';
          $content .= '<td><a href="nmv_view_med_hist_hosp?ID_med_history_hosp='.$entry->id.'">'.htmlspecialchars($entry->institution,ENT_HTML5). ' <br> ' .htmlspecialchars($entry->other_institution,ENT_HTML5).'</a></td>';
        	$content .= "<td>$entry->entry_date</td>";
        	$content .= "<td>$entry->exit_date</td>";
        	$content .= "<td>$entry->notes</td>";
        	$content .= "<td>$entry->diagnosis</td>";
        	$content .= "<td>$entry->id</td>";
            $content .= "<td>$entry->photo</td>";
        	$content .= '<td class="nowrap">';
        	$content .= createSmallButton('View Details','nmv_view_med_hist_hosp?ID_med_history_hosp='.$entry->id,'icon view');
        	if ($dbi->checkUserPermission('edit')) {
        			$content .= createSmallButton(L_EDIT,'nmv_edit_med_hist_hosp?ID_med_history_hosp='.$entry->id,'icon edit');
        	}
        	if ($dbi->checkUserPermission('admin')) {
        			$content .= createSmallButton(L_DELETE,'nmv_remove_med_hist_hosp?ID_med_history_hosp='.$entry->id,'icon delete');
        	}
        	$content .= "</td>";
        	$content .= '</tr>';
        }
        $content .= '</table>';
        if ($dbi->checkUserPermission('edit')) {
        	$content .= '<div class="buttons">';
        	$content .= createButton ('New Hospitalization Entry',
        	    'nmv_edit_med_hist_hosp?ID_victim='.$victim_id,'icon add');
        	$content .= '</div><br>';
        }

        // query: get diagnosis data
        $querystring = "
        SELECT  d.ID_med_history_diagnosis id, d.diagnosis, d.year                
        FROM nmv__med_history_diagnosis d 
        WHERE ID_victim = $victim_id
        ORDER BY d.year";
        $query = $dbi->connection->query($querystring);

        $content .= '<h3 class="tooltip">Diagnosis<span class="tooltiptext">Clinical diagnosis that is not assigned to a specific hospitalisation</span></h3>';
        $content .= '<table class="grid">';
        $content .= '<tr><th>Year</th><th>Diagnosis (trunc.)</th><th>Diagnosis Tags</th><th>ID</th><th>Options</th>';
        $content .= '</tr>';
        while ($entry = $query->fetch_object()) {
            //query get diagnosis tags
            $querystring_tag = "
                SELECT d.diagnosis
                FROM nmv__diagnosis_diagnosis dd
                LEFT JOIN nmv__diagnosis_tag d ON d.ID_diagnosis = dd.ID_diagnosis
                WHERE dd.ID_med_history_diagnosis = $entry->id";
            $tagged = $dbi->connection->query($querystring_tag);
            while ($tag = $tagged->fetch_row()) {
                $tag_array[] = $tag[0];
            }

            //table body
            $content .= '<tr>';
            $content .= "<td>$entry->year</td>";
            $content .= "<td>$entry->diagnosis</td>";
            $content .= "<td>" . implode(' | ', $tag_array) . "</td>";
            $content .= "<td>$entry->id</td>";
            $content .= '<td class="nowrap">';
        	$content .= createSmallButton('View Details','nmv_view_med_hist_diagnosis?ID_med_history_diagnosis='.$entry->id,'icon view');
        	if ($dbi->checkUserPermission('edit')) {
        			$content .= createSmallButton(L_EDIT,'nmv_edit_med_hist_diagnosis?ID_med_history_diagnosis='.$entry->id,'icon edit');
        	}
        	if ($dbi->checkUserPermission('admin')) {
        			$content .= createSmallButton(L_DELETE,'nmv_remove_med_hist_diagnosis?ID_med_history_diagnosis='.$entry->id,'icon delete');
        	}
        	$content .= "</td>";
        	$content .= '</tr>';
        }
        $content .= '</table>';
        if ($dbi->checkUserPermission('edit')) {
        	$content .= '<div class="buttons">';
        	$content .= createButton ('New Diagnosis Entry',
        	    'nmv_edit_med_hist_diagnosis?ID_victim='.$victim_id,'icon add');
        	$content .= '</div><br>';
        }

        // query: get brain research data
        $querystring = "
        SELECT h.ID_med_history_brain id, concat(IFNULL(i.institution_name, '#'),' - ',IFNULL(i.location, '#'),' - ',IFNULL(c.country, '#')) institution,
            CONCAT_WS('.', IFNULL(h.brain_report_day, '-'), IFNULL(h.brain_report_month, '-'), IFNULL(h.brain_report_year, '-')) date,
            IF(h.brain_report_has_photo, 'yes', '-') AS photo
        FROM nmv__med_history_brain h
        LEFT JOIN nmv__institution i ON i.ID_institution = h.ID_institution
        LEFT JOIN nmv__country c ON c.ID_country = i.ID_country
        WHERE ID_victim = $victim_id
        ORDER BY h.brain_report_year, h.brain_report_month, h.brain_report_day";
        $query = $dbi->connection->query($querystring);

        $content .= '<h3>Brain Report</h3>';
        $content .= '<table class="grid">';
        $content .= '<tr><th>Institution</th><th>Date<br>(D.M.Y)</th><th>ID</th><th>Photo</th><th>Options</th>';
        $content .= '</tr>';
        while ($entry = $query->fetch_object()) {
        	$content .= '<tr>';
        	$content .= '<td><a href="nmv_view_med_hist_brain?ID_med_history_brain='.$entry->id.'">'.htmlspecialchars($entry->institution,ENT_HTML5).'</a></td>';
        	$content .= "<td>$entry->date</td>";
        	$content .= "<td>$entry->id</td>";
          $content .= "<td>$entry->photo</td>";
        	$content .= '<td class="nowrap">';
        	$content .= createSmallButton('View Details','nmv_view_med_hist_brain?ID_med_history_brain='.$entry->id,'icon view');
        	if ($dbi->checkUserPermission('edit')) {
        			$content .= createSmallButton(L_EDIT,'nmv_edit_med_hist_brain?ID_med_history_brain='.$entry->id,'icon edit');
        	}
        	if ($dbi->checkUserPermission('admin')) {
        			$content .= createSmallButton(L_DELETE,'nmv_remove_med_hist_brain?ID_med_history_brain='.$entry->id,'icon delete');
        	}
        	$content .= "</td>";
        	$content .= '</tr>';
        }
        $content .= '</table>';
        if ($dbi->checkUserPermission('edit')) {
        	$content .= '<div class="buttons">';
        	$content .= createButton ('New Brain Report Entry',
        	    'nmv_edit_med_hist_brain?ID_victim='.$victim_id,'icon add');
        	$content .= '</div><br>';
        }

        // query: get brain tissue data
        $querystring = "
        SELECT h.ID_med_history_tissue id, f.tissue_form,
            s.tissue_state, h.ref_no ref_no, h.ref_no_2 ref_no_2,
            CONCAT_WS('.', IFNULL(h.since_day, '-'), IFNULL(h.since_month, '-'), IFNULL(h.since_year, '-')) date,
            CONCAT(IFNULL(i.institution_name,'unknown'), ' - ', IFNULL(i.location, '-'), ' - ', IFNULL(c.country, '-')) AS institution, h.notes notes
        FROM nmv__med_history_tissue h
        LEFT JOIN nmv__tissue_form f ON f.ID_tissue_form = h.ID_tissue_form
        LEFT JOIN nmv__tissue_state s ON s.ID_tissue_state = h.ID_tissue_state
        LEFT JOIN nmv__institution i ON i.ID_institution = h.ID_institution
        LEFT JOIN nmv__country c ON c.ID_country = i.ID_country
        WHERE ID_victim = $victim_id
        ORDER BY ref_no, FIELD(h.ID_tissue_state, 3, 5, 6, 1, 2, 7, 4), h.since_year, h.since_month, h.since_day ";
        $query = $dbi->connection->query($querystring);

        $content .= '<h3>Brain Tissue</h3>';
        $content .= '<table class="grid">';
        $content .= '<tr><th>Ref No.</th><th>2nd Ref No.</th><th>Date<br>(D.M.Y)</th><th>Tissue Form</th><th>Tissue State</th><th>Institution - Location</th><th>Notes</th></th><th>ID</th><th>Options</th>';

        $content .= '</tr>';
        while ($entry = $query->fetch_object()) {
            $content .= '<tr>';
            $content .= "<td>$entry->ref_no</td>";
             $content .= "<td>$entry->ref_no_2</td>";
            $content .= "<td>$entry->date</td>";
             $content .= '<td class="nowrap">'.htmlspecialchars($entry->tissue_form,ENT_HTML5).'</td>';
        	$content .= '<td class="nowrap">'.htmlspecialchars($entry->tissue_state,ENT_HTML5).'</td>';
        	$content .= '<td>'.htmlspecialchars($entry->institution,ENT_HTML5).'</td>';
            $content .= "<td>$entry->notes</td>";
            $content .= "<td>$entry->id</td>";
        	$content .= '<td class="nowrap">';
            $content .= createSmallButton('View Details','nmv_view_med_hist_tissue?ID_med_history_tissue='.$entry->id,'icon view');
        	if ($dbi->checkUserPermission('edit')) {
        			$content .= createSmallButton(L_EDIT,'nmv_edit_med_hist_tissue?ID_med_history_tissue='.$entry->id,'icon edit');
        	}
        	if ($dbi->checkUserPermission('admin')) {
        			$content .= createSmallButton(L_DELETE,'nmv_remove_med_hist_tissue?ID_med_history_tissue='.$entry->id,'icon delete');
        	}
        	$content .= "</td>";
        	$content .= '</tr>';
        }
        $content .= '</table>';
        if ($dbi->checkUserPermission('edit')) {
        	$content .= '<div class="buttons">';
        	$content .= createButton ('New Brain Tissue Entry',
        	    'nmv_edit_med_hist_tissue?ID_victim='.$victim_id,'icon add');
        	$content .= '</div><br>';
        }
    }

    $content .= createBackLink ('View Personal Data: '.$victim_name,'nmv_view_victim?ID_victim='.$victim_id);
}

$layout
	->set('title','Medical History: '.$victim_name)
	->set('content',$content)
	->cast();
