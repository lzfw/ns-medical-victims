<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');

$victim_id = (int) getUrlParameter('ID_victim', 0);
$source_id = (int) getUrlParameter('ID_source', 0);

$victim_name = 'Error: Missing victim.';
$source_name = 'Error: Missing source.';
$content = '';

if ($victim_id) {
    $dbi->addBreadcrumb ('Victims','nmv_list_victims');

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
        SELECT vs.ID_vict_source ID_vict_source,
            COALESCE(s.source_title, 'unspecified') title, s.creation_year year, s.medium medium,
            vs.location location, vs.ID_source ID_source
        FROM nmv__victim_source vs
        LEFT JOIN nmv__source s ON s.ID_source = vs.ID_source
        LEFT JOIN nmv__victim v ON v.ID_victim = vs.ID_victim
        WHERE vs.ID_victim = $victim_id
        ORDER BY title, year, medium
        LIMIT 300";

        $options = '';
        $row_template = ['{title}', '{year}', '{medium}', '{location}'];
        $header_template = ['Title', 'Year', 'Medium', 'Location'];

        $options .= createSmallButton('view source','nmv_view_source?ID_source={ID_source}','icon view');
        if ($dbi->checkUserPermission('edit') || $dbi->checkUserPermission('admin')) {
        	if ($dbi->checkUserPermission('edit')) {
        			$options .= createSmallButton(L_EDIT,'nmv_edit_victim_source?ID_vict_source={ID_vict_source}','icon edit');
        	}
        	if ($dbi->checkUserPermission('admin')) {
        			$options .= createSmallButton(L_DELETE,'nmv_remove_victim_source?ID_vict_source={ID_vict_source}','icon delete');
        	}
        }
        $row_template[] = $options;
        $header_template[] = L_OPTIONS;

        $content .= buildTableFromQuery(
            $querystring,
            $row_template,
            $header_template,
            'grid');

        if ($dbi->checkUserPermission('edit')) {
        	$content .= '<div class="buttons">';
        	$content .= createButton ('New source Entry',
        	    'nmv_edit_victim_source?ID_victim='.$victim_id,'icon add');
        	$content .= '</div>';
        }
    }

    $content .= createBackLink ('View victim: '.$victim_name,'nmv_view_victim?ID_victim='.$victim_id);
}

if ($source_id) {
    $dbi->addBreadcrumb ('source','nmv_list_source');

    // query: get source data
    $querystring = "
    SELECT CONCAT(COALESCE(source_title, ''), ' - ', COALESCE(creation_year, '')) source_name
    FROM nmv__source
    WHERE ID_source = $source_id";
    $query = $dbi->connection->query($querystring);
    $source = $query->fetch_object();

    if ($source) {
        $source_name = $source->source_name;

        $dbi->addBreadcrumb ($source_name,'nmv_view_source?ID_source='.$source_id);

        // query: get hosp data
        $querystring = "
        SELECT vs.ID_vict_source ID_vict_source,
            CONCAT(v.ID_victim, ': ', v.first_names, ' ', v.surname) victim_name,
            v.birth_country birth_country, v.birth_place birth_place,
            CONCAT_WS('-', v.birth_year, v.birth_month, v.birth_day) birth_date,
            vs.location location, vs.ID_victim ID_victim
        FROM nmv__victim_source vs
        LEFT JOIN nmv__source s ON s.ID_source = vs.ID_source
        LEFT JOIN nmv__victim v ON v.ID_victim = vs.ID_victim
        WHERE vs.ID_source = $source_id
        ORDER BY victim_name
        LIMIT 300";

        $options = '';
        $row_template = ['{victim_name}', '{birth_country}', '{birth_place}', '{birth_date}', '{location}'];
        $header_template = ['Victim', 'Country of Birth', 'Birth Place', 'Birth Date', 'Location'];

        $options .= createSmallButton('view Victim','nmv_view_victim?ID_victim={ID_victim}','icon view');
        if ($dbi->checkUserPermission('edit') || $dbi->checkUserPermission('admin')) {
        	if ($dbi->checkUserPermission('edit')) {
        			$options .= createSmallButton(L_EDIT,'nmv_edit_victim_source?ID_vict_source={ID_vict_source}','icon edit');
        	}
        	if ($dbi->checkUserPermission('admin')) {
        			$options .= createSmallButton(L_DELETE,'nmv_remove_victim_source?ID_vict_source={ID_vict_source}','icon delete');
        	}
        }
        $row_template[] = $options;
    	$header_template[] = L_OPTIONS;

        $content .= buildTableFromQuery(
            $querystring,
            $row_template,
            $header_template,
            'grid');

        // Not supported by nmv_edit_victim_source yet
        /*
        if ($dbi->checkUserPermission('edit')) {
        	$content .= '<div class="buttons">';
        	$content .= createButton ('New source Entry',
        	    'nmv_edit_victim_source?ID_source='.$source_id,'icon add');
        	$content .= '</div>';
        }*/
    }

    $content .= createBackLink ('View source: '.$source_name,'nmv_view_source?ID_source='.$source_id);
}

$layout
	->set('title',($victim_id ? 'Sources List: "' . $victim_name . '"' : 'Victims List: "' . $source_name . '"'))
	->set('content',$content)
	->cast();
