<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');

$victim_id = (int) getUrlParameter('ID_victim', 0);

$victim_name = 'Error: Missing victim.';
$literature_name = 'Error: Missing literature.';
$source_name = 'Error: Missing source.';

$content = '';

if ($victim_id) {
    $dbi->addBreadcrumb ('Victims','nmv_list_victims');

    // query: get victim data
    $querystring_victim = "
    SELECT CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_names, '')) victim_name
    FROM nmv__victim
    WHERE ID_victim = $victim_id";
    $query = $dbi->connection->query($querystring_victim);
    $victim = $query->fetch_object();

    if ($victim) {
        $victim_name = $victim->victim_name;

        $dbi->addBreadcrumb ($victim_name,'nmv_view_victim?ID_victim='.$victim_id);

        // query: get literature data
        $querystring_literature = "
        SELECT vl.ID_vict_lit ID_vict_lit,
            COALESCE(l.lit_title, 'unspecified') title, l.authors authors, l.lit_year year,
            vl.pages pages, vl.ID_literature ID_literature, IF(vl.literature_has_photo = -1, 'yes', '-') AS literature_has_photo,
            vl.url, CONCAT(IFNULL(vl.access_day, '-'), '.', IFNULL(vl.access_month, '-'), '.', IFNULL(vl.access_year, '-')) as access_date
        FROM nmv__victim_literature vl
        LEFT JOIN nmv__literature l ON l.ID_literature = vl.ID_literature
        LEFT JOIN nmv__victim v ON v.ID_victim = vl.ID_victim

        WHERE vl.ID_victim = $victim_id
        ORDER BY title, authors, year";

        $options = '';
        $row_template = ['{title}', '{authors}', '{year}', '{pages}', '{url}', '{access_date}', '{literature_has_photo}'];
        $header_template = ['Title', 'Authors', 'Year', 'Pages', 'URL', 'Access date', 'Contains Photo'];

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

      // new entry - button
      if ($dbi->checkUserPermission('edit')) {
        $content .= '<div class="buttons">';
        $content .= createButton ('New literature Entry',
            'nmv_edit_victim_literature?ID_victim='.$victim_id,'icon add');
        $content .= '</div>';
      }

      // table view
      $content .= buildTableFromQuery(
          $querystring_literature,
          $row_template,
          $header_template,
          'grid');

      // new entry - button
      if ($dbi->checkUserPermission('edit')) {
      	$content .= '<div class="buttons">';
      	$content .= createButton ('New literature Entry',
      	    'nmv_edit_victim_literature?ID_victim='.$victim_id,'icon add');
      	$content .= '</div>';
      }

        // query: get source data
        $querystring_source = "
        SELECT vs.ID_vict_source ID_vict_source,
            COALESCE(s.source_title, 'unspecified') title, s.creation_year year, s.medium medium,
            vs.location location, vs.ID_source ID_source, IF(vs.source_has_photo = -1, 'yes', '-') AS source_has_photo,
            vs.url, CONCAT(IFNULL(vs.access_day, '-'), '.', IFNULL(vs.access_month, '-'), '.', IFNULL(vs.access_year, '-')) as access_date
        FROM nmv__victim_source vs
        LEFT JOIN nmv__source s ON s.ID_source = vs.ID_source
        LEFT JOIN nmv__victim v ON v.ID_victim = vs.ID_victim
        WHERE vs.ID_victim = $victim_id
        ORDER BY title, year, medium";

        $content .= '<br><br><br><br><br><h2>Sources List: "' . $victim_name . '"</h2>';

        $options = '';
        $row_template = ['{title}', '{year}', '{medium}', '{location}', '{url}', '{access_date}', '{source_has_photo}'];
        $header_template = ['Title', 'Year', 'Medium', 'Location', 'URL', 'Access date', 'Contains Photo'];

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
            $querystring_source,
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



$layout
	->set('title',('Literature list: "' . $victim_name . '"'))
	->set('content',$content)
	->cast();
