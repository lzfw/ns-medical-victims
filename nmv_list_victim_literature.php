<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');

$victim_id = (int) getUrlParameter('ID_victim', 0);
$literature_id = (int) getUrlParameter('ID_literature', 0);

$victim_name = 'Error: Missing victim.';
$literature_name = 'Error: Missing literature.';
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
        SELECT vl.ID_vict_lit ID_vict_lit,
            COALESCE(l.lit_title, 'unspecified') title, l.authors authors, l.lit_year year,
            vl.pages pages, vl.ID_literature ID_literature
        FROM nmv__victim_literature vl
        LEFT JOIN nmv__literature l ON l.ID_literature = vl.ID_literature
        LEFT JOIN nmv__victim v ON v.ID_victim = vl.ID_victim
        WHERE vl.ID_victim = $victim_id
        ORDER BY title, authors, year
        LIMIT 300";

        $options = '';
        $row_template = ['{title}', '{authors}', '{year}', '{pages}'];
        $header_template = ['title', 'authors', 'year', 'pages'];

        $options .= createSmallButton('view literature','nmv_view_literature?ID_literature={ID_literature}','icon view');
        if ($dbi->checkUserPermission('edit') || $dbi->checkUserPermission('admin')) {
        	if ($dbi->checkUserPermission('edit')) {
        			$options .= createSmallButton(L_EDIT,'nmv_edit_victim_literature?ID_vict_lit={ID_vict_lit}','icon edit');
        	}
        	if ($dbi->checkUserPermission('admin')) {
        			$options .= createSmallButton(L_DELETE,'nmv_remove_victim_literature?ID_vict_lit={ID_vict_lit}','icon delete');
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
        	$content .= createButton ('New literature Entry',
        	    'nmv_edit_victim_literature?ID_victim='.$victim_id,'icon add');
        	$content .= '</div>';
        }
    }

    $content .= createBackLink ('View victim: '.$victim_name,'nmv_view_victim?ID_victim='.$victim_id);
}

if ($literature_id) {
    $dbi->addBreadcrumb ('literature','nmv_list_literature');

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

        // query: get linked data
        //complete db d (AND v.mpg_project = -1)
        if($dbi->checkUserPermission('mpg')){
            $querystring = "
            SELECT vl.ID_vict_lit ID_vict_lit,
                CONCAT(v.ID_victim, ': ', v.first_names, ' ', v.surname) victim_name,
                v.birth_place birth_place,
                CONCAT_WS('.', v.birth_day, v.birth_month, v.birth_year) birth_date,
                vl.pages pages, vl.ID_victim
            FROM nmv__victim_literature vl
            LEFT JOIN nmv__literature l ON l.ID_literature = vl.ID_literature
            LEFT JOIN nmv__victim v ON v.ID_victim = vl.ID_victim
            WHERE vl.ID_literature = $literature_id
            AND v.mpg_project = -1
            ORDER BY victim_name
            LIMIT 300";
          }else{
            $querystring = "
            SELECT vl.ID_vict_lit ID_vict_lit,
                CONCAT(v.ID_victim, ': ', v.first_names, ' ', v.surname) victim_name,
                v.birth_place birth_place,
                CONCAT_WS('.', v.birth_day, v.birth_month, v.birth_year) birth_date,
                vl.pages pages, vl.ID_victim
            FROM nmv__victim_literature vl
            LEFT JOIN nmv__literature l ON l.ID_literature = vl.ID_literature
            LEFT JOIN nmv__victim v ON v.ID_victim = vl.ID_victim
            WHERE vl.ID_literature = $literature_id
            ORDER BY victim_name
            LIMIT 300";
          }

        $options = '';
        $row_template = ['{victim_name}', '{birth_place}', '{birth_date}', '{pages}'];
        $header_template = ['Victim', 'Birth Place', 'Birth Date', 'Pages'];

        $options .= createSmallButton('view Victim','nmv_view_victim?ID_victim={ID_victim}','icon view');
        if ($dbi->checkUserPermission('edit') || $dbi->checkUserPermission('admin')) {
        	if ($dbi->checkUserPermission('edit')) {
        			$options .= createSmallButton(L_EDIT,'nmv_edit_victim_literature?ID_vict_lit={ID_vict_lit}','icon edit');
        	}
        	if ($dbi->checkUserPermission('admin')) {
        			$options .= createSmallButton(L_DELETE,'nmv_remove_victim_literature?ID_vict_lit={ID_vict_lit}','icon delete');
        	}
        }
        $row_template[] = $options;
        $header_template[] = L_OPTIONS;

        $content .= buildTableFromQuery(
            $querystring,
            $row_template,
            $header_template,
            'grid');

        // Not supported by nmv_edit_victim_literature yet
        /*
        if ($dbi->checkUserPermission('edit')) {
        	$content .= '<div class="buttons">';
        	$content .= createButton ('New literature Entry',
        	    'nmv_edit_victim_literature?ID_literature='.$literature_id,'icon add');
        	$content .= '</div>';
        }*/
    }

    $content .= createBackLink ('View literature: '.$literature_name,'nmv_view_literature?ID_literature='.$literature_id);
}

$layout
	->set('title',($victim_id ? 'Literature list: "' . $victim_name . '"' : 'Victims list: "' . $literature_name . '"'))
	->set('content',$content)
	->cast();
