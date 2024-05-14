<?php
require_once 'zefiro/ini.php';

$dbi->requireUserPermission ('view');

$dbi->addBreadcrumb (L_CONTENTS,'z_menu_contents');

// get ID_victim / _experiment
$victim_id = (int) getUrlParameter('ID_victim', 0);
$experiment_id = (int) getUrlParameter('ID_experiment', 0);

$victim_name = 'Error: Missing victim.';
$experiment_name = 'Error: Missing biomedical research.';
$title = '';
$content = '';

 //create a table of experiments a certain victim was involved in
if ($victim_id) {
    $dbi->addBreadcrumb ('Victims','nmv_list_victims');

    // query: get victim data
    $querystring = "
    SELECT CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_names, '')) AS victim_name,
            was_prisoner_assistant AS role

    FROM nmv__victim
    WHERE ID_victim = $victim_id";
    $query = $dbi->connection->query($querystring);
    $victim = $query->fetch_object();

    if ($victim) {
        $victim_name = $victim->victim_name;
        $title = 'Biomedical Research: ' . $victim_name . ' - ID ' . $victim_id;

        $dbi->addBreadcrumb ($victim_name,'nmv_view_victim?ID_victim='.$victim_id);

        //get number of experiments as a victim
        $querystring_count_v = "SELECT COUNT(ve.ID_experiment) AS total
                              FROM nmv__victim_experiment ve
                              LEFT JOIN nmv__experiment e ON e.ID_experiment = ve.ID_experiment
                              LEFT JOIN nmv__victim v ON v.ID_victim = ve.ID_victim
                              LEFT JOIN nmv__experiment_classification c on c.ID_exp_classification = e.ID_exp_classification
                              WHERE ve.ID_victim = $victim_id";
        $query_count_v = $dbi->connection->query($querystring_count_v);
        $total_results_v = $query_count_v->fetch_object();
        $experiment_count_v = $total_results_v->total;

        // query: get experiment data (victim)
        $querystring_v = "SELECT ve.ID_vict_exp AS ID_vict_exp, 
                            COALESCE(e.experiment_title, 'unspecified') AS title, c.classification, REPLACE(GROUP_CONCAT(i.institution_name SEPARATOR '<br>'), ' ', '&nbsp;') AS institution,
                            ve.ID_experiment AS ID_experiment
                        FROM nmv__victim_experiment ve
                        LEFT JOIN nmv__experiment e                 ON e.ID_experiment = ve.ID_experiment
                        LEFT JOIN nmv__experiment_institution ei    ON ei.ID_experiment = e.ID_experiment
                        LEFT JOIN nmv__institution i                ON i.ID_institution = ei.ID_institution
                        LEFT JOIN nmv__victim v                     ON v.ID_victim = ve.ID_victim
                        LEFT JOIN nmv__experiment_classification c  ON c.ID_exp_classification = e.ID_exp_classification
                        WHERE ve.ID_victim = $victim_id
                        GROUP BY ve.ID_vict_exp
                        ORDER BY exp_start_year, exp_start_month, exp_start_day, exp_end_year, exp_end_month, exp_end_day
                        ";

        //table victim
        if($victim->role != 'prisoner assistant only') {
          $options = '';
          $row_template = ['{title}', '{institution}', '{classification}'];
          $header_template = ['Title', 'Institution', 'Classification'];

          $options .= createSmallButton('View Victim-Experiment-Link','nmv_view_victim_experiment?ID_vict_exp={ID_vict_exp}','icon view');
          if ($dbi->checkUserPermission('edit') || $dbi->checkUserPermission('admin')) {
          	if ($dbi->checkUserPermission('edit')) {
          			$options .= createSmallButton(L_EDIT,'nmv_edit_victim_experiment?ID_vict_exp={ID_vict_exp}','icon edit');
          	}
          	if ($dbi->checkUserPermission('admin')) {
          			$options .= createSmallButton(L_DELETE,'nmv_remove_victim_experiment?ID_vict_exp={ID_vict_exp}','icon delete');
          	}
          }
          $options .= '<br>' . createSmallButton('View Experiment','nmv_view_experiment?ID_experiment={ID_experiment}','icon view');

      	$row_template[] = $options;
      	$header_template[] = L_OPTIONS;
        $content .= '<br><hr><br><h3>' . $victim_name . ' was victim of: </h3>';
        $content .= '<p>Number of experiments: '.$experiment_count_v.'</p>';

        // table view
        $content .= buildTableFromQuery(
            $querystring_v,
            $row_template,
            $header_template,
            'grid');

        // new entry - button
        if ($dbi->checkUserPermission('edit')) {
          $content .= '<div class="buttons">';
          $content .= createButton ('New <strong>Victim</strong> of Biomedical Research Entry',
              'nmv_edit_victim_experiment?ID_victim='.$victim_id,'icon add');
          $content .= '</div>';
        }
      }

      //-----------------------------------------------------------------------------------------------------------------------------
      //-----------------------------------------------------------------------------------------------------------------------------
      //get number of experiments as prisoner assistant
      $querystring_count_pa = "SELECT COUNT(pae.ID_experiment) AS total
                            FROM nmv__prisoner_assistant_experiment pae
                            LEFT JOIN nmv__experiment e ON e.ID_experiment = pae.ID_experiment
                            LEFT JOIN nmv__victim v ON v.ID_victim = pae.ID_victim
                            LEFT JOIN nmv__experiment_classification c on c.ID_exp_classification = e.ID_exp_classification
                            WHERE pae.ID_victim = $victim_id";
      $query_count_pa = $dbi->connection->query($querystring_count_pa);
      $total_results_pa = $query_count_pa->fetch_object();
      $experiment_count_pa = $total_results_pa->total;

      // query: get experiment data (prisoner assistant)
      $querystring_pa = "SELECT pae.ID_pa_exp AS ID_pa_exp,
                          COALESCE(e.experiment_title, 'unspecified') AS title, c.classification, REPLACE(GROUP_CONCAT(i.institution_name SEPARATOR '<br>'), ' ', '&nbsp;') AS institution,
                          pae.ID_experiment AS ID_experiment, pa.was_prisoner_assistant AS role
                      FROM nmv__prisoner_assistant_experiment pae
                      LEFT JOIN nmv__experiment e                 ON e.ID_experiment = pae.ID_experiment
                      LEFT JOIN nmv__experiment_institution ei    ON ei.ID_experiment = e.ID_experiment
                      LEFT JOIN nmv__institution i                ON i.ID_institution = ei.ID_institution
                      LEFT JOIN nmv__victim pa                    ON pa.ID_victim = pae.ID_victim
                      LEFT JOIN nmv__experiment_classification c  ON c.ID_exp_classification = e.ID_exp_classification
                      WHERE pae.ID_victim = $victim_id
                      GROUP BY pae.ID_pa_exp
                      ORDER BY exp_start_year, exp_start_month, exp_start_day, exp_end_year, exp_end_month, exp_end_day
                      ";

      // table prisoner assistant
      if($victim->role != 'victim only') {
          $options = '';
          $row_template = ['{title}', '{institution}', '{classification}'];
          $header_template = ['Title', 'Institution', 'Classification'];

          $options .= createSmallButton('View Prisoner-Assistant-Experiment-Link','nmv_view_prisoner_assistant_experiment?ID_pa_exp={ID_pa_exp}','icon view');
          if ($dbi->checkUserPermission('edit') || $dbi->checkUserPermission('admin')) {
          	if ($dbi->checkUserPermission('edit')) {
          			$options .= createSmallButton(L_EDIT,'nmv_edit_prisoner_assistant_experiment?ID_pa_exp={ID_pa_exp}','icon edit');
          	}
          	if ($dbi->checkUserPermission('admin')) {
          			$options .= createSmallButton(L_DELETE,'nmv_remove_prisoner_assistant_experiment?ID_pa_exp={ID_pa_exp}','icon delete');
          	}
          }
          $options .= '<br>' . createSmallButton('View Experiment','nmv_view_experiment?ID_experiment={ID_experiment}','icon view');

        	$row_template[] = $options;
        	$header_template[] = L_OPTIONS;
          $content .= '<br><hr><br><h3>' . $victim_name . ' was prisoner assistant in: </h3>';
          $content .= '<p>Number of experiments: '.$experiment_count_pa.'</p>';

          // table view
          $content .= buildTableFromQuery(
              $querystring_pa,
              $row_template,
              $header_template,
              'grid');

          // new entry - button
          if ($dbi->checkUserPermission('edit')) {
            $content .= '<div class="buttons">';
            $content .= createButton ('New <strong>Prisoner Assistant</strong> in Biomedical Research Entry',
                'nmv_edit_prisoner_assistant_experiment?ID_victim='.$victim_id,'icon add');
            $content .= '</div>';
          }
        }

      $content .= '<br><hr><br>';
    }

    $content .= createBackLink ('View victim: '.$victim_name,'nmv_view_victim?ID_victim='.$victim_id);
    $layout
    	->set('title', $title)
    	->set('content',$content)
    	->cast();
}


//create a table of victims of a certain experiment
if ($experiment_id) {
    // FIXME: TODO: Sad as it is, needs pagination
    $dbi->addBreadcrumb ('Biomedical research','nmv_list_experiments');

    // query: get experiment data
    $querystring = "
    SELECT CONCAT(COALESCE(experiment_title, '')) experiment_name
    FROM nmv__experiment
    WHERE ID_experiment = $experiment_id";
    $query = $dbi->connection->query($querystring);
    $experiment = $query->fetch_object();

    if ($experiment) {
        $experiment_name = $experiment->experiment_name;
        $title = 'Victims List: ' . $experiment_name;

        //browsing options --> $_GET in url
        $dbi->setUserVar('querystring', "ID_experiment=$experiment_id");
        $dbi->setUserVar('sort',getUrlParameter('sort'),'surname');
        $dbi->setUserVar('order',getUrlParameter('order'),'ASC');
        $dbi->setUserVar('skip',getUrlParameter('skip'),0);


        $dbi->addBreadcrumb ($experiment_name,'nmv_view_experiment?ID_experiment='.$experiment_id);

        // reconstruct GET-String (for scroll- and order- / sort- function)


        // query: get data of the victims of the experiment

        $querystring_items = "SELECT
                                  v.ID_victim, ve.ID_vict_exp, s.survival, v.surname AS surname, v.first_names,
                                  v.birth_year, bc.country AS birth_country,
                                  n.nationality AS nationality_1938,
                                  et.ethnic_group, ve.exp_start_day, ve.exp_start_month, ve.exp_start_year
                              FROM nmv__victim_experiment ve
                              LEFT JOIN nmv__victim v                    ON v.ID_victim = ve.ID_victim
                              LEFT JOIN nmv__survival s                  ON s.ID_survival = ve.ID_survival
                            	LEFT JOIN nmv__country bc                  ON bc.ID_country = v.ID_birth_country
                            	LEFT JOIN nmv__nationality n               ON n.ID_nationality = v.ID_nationality_1938
                            	LEFT JOIN nmv__ethnic_group et              ON et.ID_ethnic_group = v.ID_ethnic_group
                              WHERE ve.ID_experiment = $experiment_id";

        // Gesamtzahl der Suchergebnisse feststellen
        $querystring_count = "SELECT COUNT(*) AS total FROM ($querystring_items) AS xyz";
        $query_count = $dbi->connection->query($querystring_count);
        $total_results = $query_count->fetch_object();
        //$victim_count = $total_results->total;
        $dbi->setUserVar('total_results',$total_results->total);

        // order-klausel
        // version with Z_LIST_ROWS_PAGE victims per page and pagination:
        //$querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']}".($dbi->user['skip']).','.Z_LIST_ROWS_PAGE;
        //version with all victims on one page:
        $querystring_orderby = " ORDER BY {$dbi->user['sort']} {$dbi->user['order']}";

        // query ausführen
        $query_items = $dbi->connection->query($querystring_items.$querystring_orderby);

        //layout
        $layout
        	->set('title','Victims of Experiment')
        	->set('content',
              "<br><p>Title of Experiment:<strong> $experiment_name </strong><br>
              Number of Victims:<strong> $total_results->total</strong></p>"
              . createNewTabLink ('Show statistics of the experiment in new browser tab',"statistics_experiment_victims.php?ID_experiment=$experiment_id")
              . '<br>'
        	    . $dbi->getListView('table_nmv_victims_exp',$query_items)
              . createBackLink ('View Biomedical Research: '.$experiment_name,'nmv_view_experiment?ID_experiment='.$experiment_id)
              . createNewTabLink ('Show statistics of the experiment in new browser tab',"statistics_experiment_victims.php?ID_experiment=$experiment_id")
        	)
        	->cast();
    }
}
