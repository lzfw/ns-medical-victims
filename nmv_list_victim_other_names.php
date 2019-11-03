<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');

$victim_id = (int) getUrlParameter('ID_victim', 0);

$victim_name = 'Error: Missing victim.';
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

        // query: get other names
        $querystring = "
        SELECT vn.ID_name ID_name,
            vn.victim_name victim_name, vn.victim_first_names victim_first_names,
            vnt.english nametype
        FROM nmv__victim_name vn
        LEFT JOIN nmv__victim_nametype vnt ON vnt.ID_nametype = vn.nametype
        LEFT JOIN nmv__victim v ON v.ID_victim = vn.ID_victim
        WHERE vn.ID_victim = $victim_id
        ORDER BY nametype, victim_name, victim_first_names
        LIMIT 300";

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
    }

    $content .= createBackLink ('View victim: '.$victim_name,'nmv_view_victim?ID_victim='.$victim_id);
}

$layout
	->set('title', $victim_name . '\'s other names')
	->set('content',$content)
	->cast();
