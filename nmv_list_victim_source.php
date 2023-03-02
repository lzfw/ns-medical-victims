<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');

$victim_id = (int) getUrlParameter('ID_victim', 0);
$source_id = (int) getUrlParameter('ID_source', 0);
$role = getUrlParameter('role');

$victim_name = 'No person found.';
$source_name = 'No source found.';
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

        // query: get linking data
        $querystring = "
        SELECT vs.ID_vict_source ID_vict_source,
            COALESCE(s.source_title, 'unspecified') title, s.creation_year year, s.medium medium,
            vs.location location, vs.ID_source ID_source, IF(vs.source_has_photo = -1, 'yes', '-') AS source_has_photo
        FROM nmv__victim_source vs
        LEFT JOIN nmv__source s ON s.ID_source = vs.ID_source
        LEFT JOIN nmv__victim v ON v.ID_victim = vs.ID_victim
        WHERE vs.ID_victim = $victim_id
        ORDER BY title, year, medium";

        $options = '';
        $row_template = ['{title}', '{year}', '{medium}', '{location}', '{source_has_photo}'];
        $header_template = ['Title', 'Year', 'Medium', 'Location in Source', 'Contains Photo'];

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

        // new entry - button
        if ($dbi->checkUserPermission('edit')) {
          $content .= '<div class="buttons">';
          $content .= createButton ('New source Entry',
              'nmv_edit_victim_source?ID_victim='.$victim_id,'icon add');
          $content .= '</div>';
        }

        // table view
        $content .= buildTableFromQuery(
            $querystring,
            $row_template,
            $header_template,
            'grid');

        // new entry - button
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

        //browsing options --> $_GET in url
        $dbi->setUserVar('querystring', "ID_source=$source_id");
        $dbi->setUserVar('sort',getUrlParameter('sort'),'surname');
        $dbi->setUserVar('order',getUrlParameter('order'),'ASC');
        $dbi->setUserVar('skip',getUrlParameter('skip'),0);

        $dbi->addBreadcrumb ($source_name,'nmv_view_source?ID_source='.$source_id);

        // query: get linking data
        $querystring = "SELECT vs.ID_vict_source ID_vict_source,
              v.surname surname, v.first_names first_names,
              v.birth_place birth_place, v.birth_year birth_year,
              CONCAT_WS('.', v.birth_day, v.birth_month, v.birth_year) birth_date,
              vs.location location, vs.ID_victim ID_victim, IF(vs.source_has_photo = -1, 'yes', '-') AS source_has_photo
          FROM nmv__victim_source vs
          LEFT JOIN nmv__source s ON s.ID_source = vs.ID_source
          LEFT JOIN nmv__victim v ON v.ID_victim = vs.ID_victim
          WHERE vs.ID_source = $source_id";
          $querystring_count = "SELECT COUNT(DISTINCT v.ID_victim) AS total
                                FROM nmv__victim_source vs
                                LEFT JOIN nmv__victim v ON v.ID_victim = vs.ID_victim
                                WHERE vs.ID_source = $source_id";

        //complete db d(AND v.mpg_project = -1)
        if($dbi->checkUserPermission('mpg')) {
          $querystring .= " AND v.mpg_project = -1";
          $querystring_count .= " AND v.mpg_project = -1";
        }
        if($role == 'victim') {
          $querystring .= " AND v.was_prisoner_assistant != 'prisoner assistant only'";
          $querystring_count .= " AND v.was_prisoner_assistant != 'prisoner assistant only'";
        } elseif($role == 'prisoner_assistant') {
          $querystring .= " AND v.was_prisoner_assistant != 'victim only'";
          $querystring_count .= " AND v.was_prisoner_assistant != 'victim only'";
        }

        // Gesamtanzahl der Suchergebnisse feststellen
        $query_count = $dbi->connection->query($querystring_count);
        $total_results = $query_count->fetch_object();
        $dbi->setUserVar('total_results',$total_results->total);
        // order-klausel
        $querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']} ";

        // query ausführen
        $query_items = $dbi->connection->query($querystring.$querystring_orderby);

        $content .= 'Number of victims: '. $total_results->total. '</p>';

        $options = '';
        $row_template = ['{ID_victim}', '{surname}', '{birth_place}', '{birth_date}', '{location}', '{source_has_photo}'];
        $header_template = ['ID', 'Surname', 'Birth Place', 'Birth Date', 'Location in Source', 'Source Contains Photo'];


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
        $query_count = $dbi->connection->query($querystring_count);
        $total_results = $query_count->fetch_object();

        // new entry - button
        if ($dbi->checkUserPermission('edit')) {
            $content .= '<div class="buttons">';
            $content .= createButton ('New Person Link',
                'nmv_edit_victim_source?ID_source='.$source_id,'icon add');
            $content .= '</div>';
        }

        $content .= buildTableFromQuery(
          $querystring,
          $row_template,
          $header_template,
          'grid');

        // new entry - button
        if ($dbi->checkUserPermission('edit')) {
            $content .= '<div class="buttons">';
            $content .= createButton ('New Person Link',
                'nmv_edit_victim_source?ID_source='.$source_id,'icon add');
            $content .= '</div>';
        }
    }

    //$content .= createBackLink ('View source: '.$source_name,'nmv_view_source?ID_source='.$source_id);
}

$layout
	->set('title',($victim_id ? 'Sources List: "' . $victim_name . '"' : 'Persons List: "' . $source_name . '"'))
	->set('content',$content)
	->cast();
