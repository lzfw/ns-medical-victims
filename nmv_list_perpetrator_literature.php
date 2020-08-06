<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');

$perpetrator_id = (int) getUrlParameter('ID_perpetrator', 0);
$literature_id = (int) getUrlParameter('ID_literature', 0);

$perpetrator_name = 'Error: Missing perpetrator.';
$literature_name = 'Error: Missing literature.';
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

        // query: get literature data
        $querystring = "
        SELECT pl.ID_perp_lit ID_perp_lit,
            COALESCE(l.lit_title, 'unspecified') title, l.authors authors, l.lit_year year,
            pl.pages pages, pl.ID_literature ID_literature
        FROM nmv__perpetrator_literature pl
        LEFT JOIN nmv__literature l ON l.ID_literature = pl.ID_literature
        LEFT JOIN nmv__perpetrator p ON p.ID_perpetrator = pl.ID_perpetrator
        WHERE pl.ID_perpetrator = $perpetrator_id
        ORDER BY title, authors, year
        LIMIT 300";

        $options = '';
        $row_template = ['{title}', '{authors}', '{year}', '{pages}'];
        $header_template = ['Title', 'Authors', 'Year', 'Pages'];

        $options .= createSmallButton('View Literature','nmv_view_literature?ID_literature={ID_literature}','icon view');
        if ($dbi->checkUserPermission('edit') || $dbi->checkUserPermission('admin')) {
        	if ($dbi->checkUserPermission('edit')) {
        			$options .= createSmallButton(L_EDIT,'nmv_edit_perpetrator_literature?ID_perp_lit={ID_perp_lit}','icon edit');
        	}
        	if ($dbi->checkUserPermission('admin')) {
        			$options .= createSmallButton(L_DELETE,'nmv_remove_perpetrator_literature?ID_perp_lit={ID_perp_lit}','icon delete');
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
        	$content .= createButton ('New Literature Entry',
        	    'nmv_edit_perpetrator_literature?ID_perpetrator='.$perpetrator_id,'icon add');
        	$content .= '</div>';
        }
    }

    $content .= createBackLink ('View perpetrator: '.$perpetrator_name,'nmv_view_perpetrator?ID_perpetrator='.$perpetrator_id);
}

if ($literature_id) {
    $dbi->addBreadcrumb ('Literature','nmv_list_literature');

    // query: get literature data
    $querystring = "
    SELECT CONCAT(COALESCE(lit_title, ''), ' - ', COALESCE(authors, '')) literature_name
    FROM nmv__literature
    WHERE ID_literature = $literature_id";
    $query = $dbi->connection->query($querystring);
    $literature = $query->fetch_object();

    if ($literature) {
        $literature_name = $literature->literature_name;

        $dbi->addBreadcrumb ($literature_name,'nmv_view_literature?ID_literature='.$literature_id);

        // query: get perpetrator data
        $querystring = "
        SELECT pl.ID_perp_lit ID_perp_lit,
            CONCAT(p.ID_perpetrator, ': ', p.first_names, ' ', p.surname) perpetrator_name,
            p.birth_country birth_country, p.birth_place birth_place,
            CONCAT_WS('-', p.birth_year, p.birth_month, p.birth_day) birth_date,
            pl.pages pages, pl.ID_perpetrator
        FROM nmv__perpetrator_literature pl
        LEFT JOIN nmv__literature l ON l.ID_literature = pl.ID_literature
        LEFT JOIN nmv__perpetrator p ON p.ID_perpetrator = pl.ID_perpetrator
        WHERE pl.ID_literature = $literature_id
        ORDER BY perpetrator_name
        LIMIT 300";

        $options = '';
        $row_template = ['{perpetrator_name}', '{birth_country}', '{birth_place}', '{birth_date}', '{pages}'];
        $header_template = ['Perpetrator', 'Country of Birth', 'Birth Place', 'Birth Date', 'Pages'];

        $options .= createSmallButton('View Perpetrator','nmv_view_perpetrator?ID_perpetrator={ID_perpetrator}','icon view');
        if ($dbi->checkUserPermission('edit') || $dbi->checkUserPermission('admin')) {
        	if ($dbi->checkUserPermission('edit')) {
        			$options .= createSmallButton(L_EDIT,'nmv_edit_perpetrator_literature?ID_perp_lit={ID_perp_lit}','icon edit');
        	}
        	if ($dbi->checkUserPermission('admin')) {
        			$options .= createSmallButton(L_DELETE,'nmv_remove_perpetrator_literature?ID_perp_lit={ID_perp_lit}','icon delete');
        	}
        }
    	$row_template[] = $options;
    	$header_template[] = L_OPTIONS;

        $content .= buildTableFromQuery(
            $querystring,
            $row_template,
            $header_template,
            'grid');

        // Not supported by nmv_edit_perpetrator_literature yet
        /*
        if ($dbi->checkUserPermission('edit')) {
        	$content .= '<div class="buttons">';
        	$content .= createButton ('New Literature Entry',
        	    'nmv_edit_perpetrator_literature?ID_literature='.$literature_id,'icon add');
        	$content .= '</div>';
        }*/
    }

    $content .= createBackLink ('View Literature: '.$literature_name,'nmv_view_literature?ID_literature='.$literature_id);
}

$layout
	->set('title',($perpetrator_id ? 'Literature List: "' . $perpetrator_name . '"' : 'Perpetrators List: "' . $literature_name . '"'))
	->set('content',$content)
	->cast();
