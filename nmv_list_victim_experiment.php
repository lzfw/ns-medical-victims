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
    SELECT CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_names, '')) victim_name
    FROM nmv__victim
    WHERE ID_victim = $victim_id";
    $query = $dbi->connection->query($querystring);
    $victim = $query->fetch_object();

    if ($victim) {
        $victim_name = $victim->victim_name;
        $title = 'Biomedical Research: ' . $victim_name;

        $dbi->addBreadcrumb ($victim_name,'nmv_view_victim?ID_victim='.$victim_id);

        //get number of experiments
        $querystring_count = "
        SELECT COUNT(ve.ID_experiment) AS total
          FROM nmv__victim_experiment ve
          LEFT JOIN nmv__experiment e ON e.ID_experiment = ve.ID_experiment
          LEFT JOIN nmv__victim v ON v.ID_victim = ve.ID_victim
          LEFT JOIN nmv__experiment_classification c on c.ID_exp_classification = e.classification
          WHERE ve.ID_victim = $victim_id";
        $query_count = $dbi->connection->query($querystring_count);
        $total_results = $query_count->fetch_object();
        $experiment_count = $total_results->total;


        // query: get experiment data
        $querystring = "
        SELECT ve.ID_vict_exp ID_vict_exp, ve.experiment_duration duration, ve.age_experiment_start age,
            COALESCE(e.experiment_title, 'unspecified') title, c.english classification,
            ve.ID_experiment ID_experiment
        FROM nmv__victim_experiment ve
        LEFT JOIN nmv__experiment e ON e.ID_experiment = ve.ID_experiment
        LEFT JOIN nmv__victim v ON v.ID_victim = ve.ID_victim
        LEFT JOIN nmv__experiment_classification c on c.ID_exp_classification = e.classification
        WHERE ve.ID_victim = $victim_id
        ORDER BY exp_start_year, exp_start_month, exp_start_day, exp_end_year, exp_end_month, exp_end_day
        ";

        $options = '';
        $row_template = ['{title}', '{classification}', '{duration}', '{age}'];
        $header_template = ['Title', 'Classification', 'Duration', 'Age'];

        $options .= createSmallButton('View Victim-Research','nmv_view_victim_experiment?ID_vict_exp={ID_vict_exp}','icon view');
        if ($dbi->checkUserPermission('edit') || $dbi->checkUserPermission('admin')) {
        	if ($dbi->checkUserPermission('edit')) {
        			$options .= createSmallButton(L_EDIT,'nmv_edit_victim_experiment?ID_vict_exp={ID_vict_exp}','icon edit');
        	}
        	if ($dbi->checkUserPermission('admin')) {
        			$options .= createSmallButton(L_DELETE,'nmv_remove_victim_experiment?ID_vict_exp={ID_vict_exp}','icon delete');
        	}
        }
    	$row_template[] = $options;
    	$header_template[] = L_OPTIONS;

        $content .= '<p>Number of experiments: '.$experiment_count.'</p>';
        $content .= buildTableFromQuery(
            $querystring,
            $row_template,
            $header_template,
            'grid');

        if ($dbi->checkUserPermission('edit')) {
        	$content .= '<div class="buttons">';
        	$content .= createButton ('New Biomedical Research Entry',
        	    'nmv_edit_victim_experiment?ID_victim='.$victim_id,'icon add');
        	$content .= '</div>';
        }
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
    SELECT CONCAT(COALESCE(experiment_title, ''), ' - ', COALESCE(field_of_interest, '')) experiment_name
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

        $querystring_items = "
        SELECT
            v.ID_victim, ve.ID_vict_exp, s.english AS survival, v.surname AS surname, v.first_names,
            v.birth_place, v.birth_year, v.birth_month, v.birth_day, bc.english AS birth_country,
            m.english AS marital_family_status, e.english AS education,
            v.death_year, v.death_month, v.death_day, v.death_place, dc.english AS death_country,
            v.cause_of_death, v.gender, r.english AS religion, n.english AS nationality_1938,
            et.english AS ethnic_group, o.english AS occupation, v.occupation_details, v.twin,
            v.arrest_location, ac.english AS arrest_country,
            v.residence_after_1945_place, v.residence_after_1945_country, v.occupation_after_1945, na.english AS nationality_after_1945,
            v.mpg_project, da.work_group  AS dataset_origin, es.english as evaluation_status, ve.exp_start_day, ve.exp_start_month, ve.exp_start_year
        FROM nmv__victim_experiment ve
        LEFT JOIN nmv__victim v                    ON v.ID_victim = ve.ID_victim
        LEFT JOIN nmv__survival s                  ON s.ID_survival = ve.ID_survival
        LEFT JOIN nmv__marital_family_status m     ON m.ID_marital_family_status = v.ID_marital_family_status
      	LEFT JOIN nmv__education e                 ON e.ID_education = v.ID_education
      	LEFT JOIN nmv__country bc                  ON bc.ID_country = v.ID_birth_country
      	LEFT JOIN nmv__country dc                  ON dc.ID_country = v.ID_death_country
      	LEFT JOIN nmv__country ac                  ON ac.ID_country = v.ID_arrest_country
      	LEFT JOIN nmv__religion r                  ON r.ID_religion = v.religion
      	LEFT JOIN nmv__nationality n               ON n.ID_nationality = v.nationality_1938
      	LEFT JOIN nmv__nationality na              ON na.ID_nationality = v.nationality_after_1945
      	LEFT JOIN nmv__ethnicgroup et              ON et.ID_ethnicgroup = v.ethnic_group
      	LEFT JOIN nmv__occupation o                ON o.ID_occupation = v.occupation
      	LEFT JOIN nmv__dataset_origin da           ON da.ID_dataset_origin = v.ID_dataset_origin
      	LEFT JOIN nmv__evaluation ev               ON ev.ID_victim = v.ID_victim
        LEFT JOIN nmv__victim_evaluation_status es ON es.ID_status = ev.evaluation_status
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
