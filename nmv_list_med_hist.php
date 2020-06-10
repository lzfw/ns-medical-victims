<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');
$dbi->addBreadcrumb ('Victims','nmv_list_victims');

$victim_id = (int) getUrlParameter('ID_victim', 0);
$victim_name = 'Error: Missing victim.';
$content = '';

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
        SELECT h.ID_med_history_hosp id, LEFT(concat(IFNULL(LEFT(i.institution_name, 60), '#'),' - ',IFNULL(LEFT(i.location,40), '#'),' - ',IFNULL(i.country, '#')),100) institution,
            CONCAT_WS('-', h.date_entry_year, h.date_entry_month, h.date_entry_day) date
        FROM nmv__med_history_hosp h
        LEFT JOIN nmv__institution i ON i.ID_institution = h.ID_institution
        WHERE ID_victim = $victim_id
        ORDER BY date
        LIMIT 300";
        $query = $dbi->connection->query($querystring);

        $content .= '<h3>Hospitalization</h3>';
        $content .= '<table class="grid">';
        $content .= '<tr><th>Institution</th><th>Date</th><th>ID</th><th>Options</th>';
        $content .= '</tr>';
        while ($entry = $query->fetch_object()) {
        	$content .= '<tr>';
        	$content .= '<td><a href="nmv_view_med_hist_hosp?ID_med_history_hosp='.$entry->id.'">'.htmlspecialchars($entry->institution,ENT_HTML5).'</a></td>';
        	$content .= "<td>$entry->date</td>";
        	$content .= "<td>$entry->id</td>";
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
        	$content .= '</div>';
        }

        // query: get brain research data
        $querystring = "
        SELECT h.ID_med_history_brain id, LEFT(concat(IFNULL(LEFT(i.institution_name, 60), '#'),' - ',IFNULL(LEFT(i.location,40), '#'),' - ',IFNULL(i.country, '#')),100) institution,
            CONCAT_WS('-', h.brain_report_year, h.brain_report_month, h.brain_report_day) date
        FROM nmv__med_history_brain h
        LEFT JOIN nmv__institution i ON i.ID_institution = h.ID_institution
        WHERE ID_victim = $victim_id
        ORDER BY date
        LIMIT 300";
        $query = $dbi->connection->query($querystring);

        $content .= '<h3>Brain Research</h3>';
        $content .= '<table class="grid">';
        $content .= '<tr><th>Institution</th><th>Date</th><th>ID</th><th>Options</th>';
        $content .= '</tr>';
        while ($entry = $query->fetch_object()) {
        	$content .= '<tr>';
        	$content .= '<td><a href="nmv_view_med_hist_brain?ID_med_history_brain='.$entry->id.'">'.htmlspecialchars($entry->institution,ENT_HTML5).'</a></td>';
        	$content .= "<td>$entry->date</td>";
        	$content .= "<td>$entry->id</td>";
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
        	$content .= createButton ('New Brain Research Entry',
        	    'nmv_edit_med_hist_brain?ID_victim='.$victim_id,'icon add');
        	$content .= '</div>';
        }

        // query: get brain tissue data
        $querystring = "
        SELECT h.ID_med_history_tissue id, f.english tissue_form,
            s.english tissue_state, location, h.ref_no ref_no,
            CONCAT_WS('-', h.since_year, h.since_month, h.since_day) date
        FROM nmv__med_history_tissue h
        LEFT JOIN nmv__tissue_form f ON f.ID_tissue_form = h.ID_tissue_form
        LEFT JOIN nmv__tissue_state s ON s.ID_tissue_state = h.ID_tissue_state
        WHERE ID_victim = $victim_id
        ORDER BY ref_no, date
        LIMIT 300";
        $query = $dbi->connection->query($querystring);

        $content .= '<h3>Brain Tissues</h3>';
        $content .= '<table class="grid">';
        $content .= '<tr><th>Ref No.</th><th>Date (Y-M-D)</th><th>Tissue Form</th><th>Tissue State</th><th>Location</th><th>ID</th><th>Options</th>';

        $content .= '</tr>';
        while ($entry = $query->fetch_object()) {
        	$content .= '<tr>';
          $content .= "<td>$entry->ref_no</td>";
          $content .= "<td>$entry->date</td>";
          $content .= '<td>'.htmlspecialchars($entry->tissue_form,ENT_HTML5).'</td>';
        	$content .= '<td>'.htmlspecialchars($entry->tissue_state,ENT_HTML5).'</td>';
        	$content .= '<td>'.htmlspecialchars($entry->location,ENT_HTML5).'</td>';
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
        	$content .= '</div>';
        }
    }

    $content .= createBackLink ('View Victim: '.$victim_name,'nmv_view_victim?ID_victim='.$victim_id);
}

$layout
	->set('title','Medical History: '.$victim_name)
	->set('content',$content)
	->cast();
