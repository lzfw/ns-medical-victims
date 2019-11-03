<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');

// query: get victim data
$querystring = "SELECT `ID_victim`, `surname`, `first_names`, `birth_place` FROM nmv__victim ORDER BY `surname` DESC LIMIT 10";
$query = $dbi->connection->query($querystring);
echo $dbi->connection->error;

$content = '';
$content .= '<table class="grid">';
$content .= '<tr><th>surname</th><th>first names</th><th>id</th><th>birth place</th>';
if ($dbi->checkUserPermission('admin')) {
	$content .= '<th>'.L_OPTIONS.'</th>';
}
$content .= '</tr>';
while ($victim = $query->fetch_object()) {
	$content .= '<tr>';
	$content .= '<td><a href="nmv_view_victim?ID_victim='.$victim->ID_victim.'">'.htmlspecialchars($victim->surname,ENT_HTML5).'</a></td>';
	$content .= '<td>'.htmlspecialchars($victim->first_names,ENT_HTML5).'</td>';
	$content .= "<td>$victim->ID_victim</td>";
	$content .= '<td>'.htmlspecialchars($victim->birth_place,ENT_HTML5).'</td>';
	$content .= '<td class="nowrap">';
	if ($dbi->checkUserPermission('edit')) {
			$content .= createSmallButton(L_EDIT,'nmv_edit_victim?ID_victim='.$victim->ID_victim,'icon edit');
	}
	if ($dbi->checkUserPermission('admin')) {
			$content .= createSmallButton(L_DELETE,'nmv_remove_victim?ID_victim='.$victim->ID_victim,'icon delete');
	}
	$content .= createSmallButton("medical history",'nmv_list_med_hist?ID_victim='.$victim->ID_victim,'icon report-paper');
	$content .= "</td>";
	$content .= '</tr>';
}
$content .= '</table>';
if ($dbi->checkUserPermission('edit')) {
	$content .= '<div class="buttons">';
	$content .= createButton ('New Victim','nmv_edit_victim','icon addUser');
	$content .= '</div>';
}
$content .= createBackLink (L_CONTENTS,'z_menu_contents');

$layout
	->set('title','Victims')
	->set('content',$content)
	->cast();
