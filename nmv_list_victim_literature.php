<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');

$victim_id = (int) getUrlParameter('ID_victim', 0);
$literature_id = (int) getUrlParameter('ID_literature', 0);
$role = getUrlParameter('role');

$victim_name = 'No entries found.';
$literature_name = 'No entries found.';
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
            vl.pages pages, vl.ID_literature ID_literature, IF(vl.literature_has_photo = -1, 'yes', '-') AS literature_has_photo
        FROM nmv__victim_literature vl
        LEFT JOIN nmv__literature l ON l.ID_literature = vl.ID_literature
        LEFT JOIN nmv__victim v ON v.ID_victim = vl.ID_victim
        WHERE vl.ID_victim = $victim_id
        ORDER BY title, authors, year";

        $options = '';
        $row_template = ['{title}', '{authors}', '{year}', '{pages}', '{literature_has_photo}'];
        $header_template = ['Title', 'Authors', 'Year', 'Pages in Literature', 'Literature Contains Photo'];

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

        if ($dbi->checkUserPermission('edit')) {
            $content .= '<div class="buttons">';
            $content .= createButton ('New literature Entry',
                'nmv_edit_victim_literature?ID_victim='.$victim_id,'icon add');
            $content .= '</div>';
        }

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
          $querystring = "
          SELECT vl.ID_vict_lit ID_vict_lit,
              CONCAT(v.ID_victim, ': ', COALESCE(v.first_names, ''), ' ', COALESCE(v.surname, '')) victim_name,
              v.birth_place birth_place,
              CONCAT_WS('.', v.birth_day, v.birth_month, v.birth_year) birth_date,
              vl.pages pages, vl.ID_victim, IF(vl.literature_has_photo = -1, 'yes', '-') AS literature_has_photo
          FROM nmv__victim_literature vl
          LEFT JOIN nmv__literature l ON l.ID_literature = vl.ID_literature
          LEFT JOIN nmv__victim v ON v.ID_victim = vl.ID_victim
          WHERE vl.ID_literature = $literature_id";

        //complete db d (AND v.mpg_project = -1)
        if($dbi->checkUserPermission('mpg')){
          $querystring .= " AND v.mpg_project = -1";
        }
        if($role == 'victim') {
          $querystring .= " AND v.was_prisoner_assistant != 'prisoner assistant only'";
        } elseif($role == 'prisoner_assistant') {
          $querystring .= " AND v.was_prisoner_assistant != 'victim only'";
        }

        $querystring .= " ORDER BY victim_name";

        $options = '';
        $row_template = ['{victim_name}', '{birth_place}', '{birth_date}', '{pages}', '{literature_has_photo}'];
        $header_template = ['Person', 'Birth Place', 'Birth Date', 'Pages in Literature', 'Literature Contains Photo'];

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

        if ($dbi->checkUserPermission('edit')) {
            $content .= '<div class="buttons">';
            $content .= createButton ('New Person Link',
                'nmv_edit_victim_literature?ID_literature='.$literature_id,'icon add');
            $content .= '</div>';
        }

        $content .= buildTableFromQuery(
            $querystring,
            $row_template,
            $header_template,
            'grid');

        if ($dbi->checkUserPermission('edit')) {
            $content .= '<div class="buttons">';
            $content .= createButton ('New Person Link',
                'nmv_edit_victim_literature?ID_literature='.$literature_id,'icon add');
            $content .= '</div>';
        }
    }

    $content .= createBackLink ('View literature: '.$literature_name,'nmv_view_literature?ID_literature='.$literature_id);
}

$layout
	->set('title',($victim_id ? 'Literature list: "' . $victim_name . '"' : 'Persons list: "' . $literature_name . '"'))
	->set('content',$content)
	->cast();
