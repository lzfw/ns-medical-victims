<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');

$perpetrator_id = (int) getUrlParameter('ID_perpetrator', 0);
$source_id = (int) getUrlParameter('ID_source', 0);

$perpetrator_name = 'Error: Missing perpetrator.';
$source_name = 'Error: Missing source.';
$content = '';

if ($perpetrator_id) {
    $dbi->addBreadcrumb ('Perpetrators','nmv_list_perpetrators');

    // query: get perpetrator data
    $querystring = "
    SELECT CONCAT(COALESCE(titles, ''), ' ', COALESCE(surname, ''), ' ', COALESCE(first_names, '')) perpetrator_name
    FROM nmv__perpetrator
    WHERE ID_perpetrator = $perpetrator_id";
    $query = $dbi->connection->query($querystring);
    $perpetrator = $query->fetch_object();

    if ($perpetrator) {
        $perpetrator_name = $perpetrator->perpetrator_name;

        $dbi->addBreadcrumb ($perpetrator_name,'nmv_view_perpetrator?ID_perpetrator='.$perpetrator_id);

        // query: get source data
        $querystring = "
        SELECT ps.ID_perp_source ID_perp_source,
            COALESCE(s.source_title, 'unspecified') title, s.creation_year year,
            ps.location location, ps.ID_source ID_source,
            ps.url, CONCAT(IFNULL(ps.access_day, '-'), '.', IFNULL(ps.access_month, '-'), '.', IFNULL(ps.access_year, '-')) as access_date
        FROM nmv__perpetrator_source ps
        LEFT JOIN nmv__source s ON s.ID_source = ps.ID_source
        LEFT JOIN nmv__perpetrator p ON p.ID_perpetrator = ps.ID_perpetrator
        WHERE ps.ID_perpetrator = $perpetrator_id
        ORDER BY title, year";

        $options = '';
        $row_template = ['{title}', '{year}','{location}', '{url}', '{access_date}'];
        $header_template = ['Title', 'Year','Location in Source', 'URL', 'Access Date dmY'];

        $options .= createSmallButton('View Source','nmv_view_source?ID_source={ID_source}','icon view');
        if ($dbi->checkUserPermission('edit') || $dbi->checkUserPermission('admin')) {
        	if ($dbi->checkUserPermission('edit')) {
        			$options .= createSmallButton(L_EDIT,"nmv_edit_perpetrator_source?ID_perp_source={ID_perp_source}&ID_perpetrator=$perpetrator_id",'icon edit');
        	}
        	if ($dbi->checkUserPermission('admin')) {
        			$options .= createSmallButton(L_DELETE,'nmv_remove_perpetrator_source?ID_perp_source={ID_perp_source}','icon delete');
        	}
        }
    	$row_template[] = $options;
    	$header_template[] = L_OPTIONS;

      // new entry - button
      if ($dbi->checkUserPermission('edit')) {
      	$content .= '<div class="buttons">';
      	$content .= createButton ('New source Entry',
      	    'nmv_edit_perpetrator_source?ID_perpetrator='.$perpetrator_id,'icon add');
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
      	    'nmv_edit_perpetrator_source?ID_perpetrator='.$perpetrator_id,'icon add');
      	$content .= '</div>';
      }
    }

    $content .= createBackLink ('View perpetrator: '.$perpetrator_name,'nmv_view_perpetrator?ID_perpetrator='.$perpetrator_id);
}

if ($source_id) {
    $dbi->addBreadcrumb ('Sources','nmv_list_source');

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

        // query: get source data
        $querystring = "
        SELECT ps.ID_perp_source ID_perp_source,
            CONCAT(p.ID_perpetrator, ': ', p.first_names, ' ', p.surname) perpetrator_name,
            p.birth_place birth_place,
            CONCAT_WS('.', p.birth_day, p.birth_month, p.birth_year) birth_date,
            ps.url, CONCAT(IFNULL(ps.access_day, '-'), '.', IFNULL(ps.access_month, '-'), '.', IFNULL(ps.access_year, '-')) as access_date,
            ps.location location, ps.ID_perpetrator ID_perpetrator
        FROM nmv__perpetrator_source ps
        LEFT JOIN nmv__source s ON s.ID_source = ps.ID_source
        LEFT JOIN nmv__perpetrator p ON p.ID_perpetrator = ps.ID_perpetrator
        WHERE ps.ID_source = $source_id
        ORDER BY perpetrator_name";

        $options = '';
        $row_template = ['{perpetrator_name}', '{birth_place}', '{birth_date}', '{location}', '{url}', '{access_date}'];
        $header_template = ['Perpetrator', 'Birth Place', 'Birth Date', 'Location in Source', 'URL', 'Access Date dmY'];

        $options .= createSmallButton('View Perpetrator','nmv_view_perpetrator?ID_perpetrator={ID_perpetrator}','icon view');
        if ($dbi->checkUserPermission('edit') || $dbi->checkUserPermission('admin')) {
        	if ($dbi->checkUserPermission('edit')) {
        			$options .= createSmallButton(L_EDIT,"nmv_edit_perpetrator_source?ID_perp_source={ID_perp_source}&ID_source=$source_id",'icon edit');
        	}
        	if ($dbi->checkUserPermission('admin')) {
        			$options .= createSmallButton(L_DELETE,'nmv_remove_perpetrator_source?ID_perp_source={ID_perp_source}','icon delete');
        	}
        }
    	$row_template[] = $options;
    	$header_template[] = L_OPTIONS;

        // new entry - button
        if ($dbi->checkUserPermission('edit')) {
            $content .= '<div class="buttons">';
            $content .= createButton ('New Perpetrator Entry',
                'nmv_edit_perpetrator_source?ID_source='.$source_id,'icon add');
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
            $content .= createButton ('New Perpetrator Entry',
                'nmv_edit_perpetrator_source?ID_source='.$source_id,'icon add');
            $content .= '</div>';
        }

        // Not supported by nmv_edit_perpetrator_source yet
        /*
        if ($dbi->checkUserPermission('edit')) {
        	$content .= '<div class="buttons">';
        	$content .= createButton ('New source Entry',
        	    'nmv_edit_perpetrator_source?ID_source='.$source_id,'icon add');
        	$content .= '</div>';
        }*/
    }

    $content .= createBackLink ('View Source: '.$source_name,'nmv_view_source?ID_source='.$source_id);
}

$layout
	->set('title',($perpetrator_id ? 'Sources List: "' . $perpetrator_name . '"' : 'Perpetrators List: "' . $source_name . '"'))
	->set('content',$content)
	->cast();
