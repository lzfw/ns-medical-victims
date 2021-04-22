<?php
/**
*creates statistics for victims of a certain experiment
*
*
*
*/


require_once 'zefiro/ini.php';
require_once 'flotilla/ini.php';

$dbi->requireUserPermission ('view');
//complete db
$dbi->denyUserPermission ('mpg');


// Select experiment
$experiment_select = new Form ('experiment_victims','statistics_experiment_victims','GET');
$experiment_select->addConnection(MYSQL_DB,$db_host,$db_user,$db_pass,$db_name);
$experiment_select->addField ('ID_experiment',SELECT)
  ->setLabel('Experiment')
  ->addOption (NO_VALUE, 'please choose experiment')
  ->addOptionsFromQuery(" SELECT ID_experiment AS value, CONCAT(ID_experiment, ' - ', IFNULL(experiment_title, ' ')) AS title
                          FROM nmv__experiment
                          ORDER BY experiment_title");
$experiment_select
  ->addButton(SUBMIT,'OK');

//get experiment-data
$experiment_id = (int) getUrlParameter('ID_experiment', 0);
$experiment_query = "SELECT experiment_title
          FROM nmv__experiment
          WHERE ID_experiment=$experiment_id";
$query_item = $dbi->connection->query($experiment_query)->fetch_array();
$experiment_title = $query_item['experiment_title'];


//get the data: queries
$total_number_query = "SELECT COUNT(ve.ID_victim) AS count_total
                      FROM nmv__victim_experiment ve
                      WHERE ve.ID_experiment = $experiment_id";
$total_number_query_item = $dbi->connection->query($total_number_query)->fetch_array();
$total_number = $total_number_query_item['count_total'];

$survival_query =  "SELECT s.english AS survival, COUNT(ve.ID_victim) AS count_survival
                    FROM nmv__victim_experiment ve
                    LEFT JOIN nmv__survival s ON s.ID_survival = ve.ID_survival
                    WHERE ve.ID_experiment = $experiment_id
                    GROUP BY ve.ID_survival";
$ethnicity_query = "SELECT et.english AS ethnic_group, COUNT(ve.ID_victim) AS count_ethnic_group
                    FROM nmv__victim_experiment ve
                    LEFT JOIN nmv__victim v ON v.ID_victim = ve.ID_victim
                    LEFT JOIN nmv__ethnicgroup et ON et.ID_ethnicgroup = v.ethnic_group
                    WHERE ve.ID_experiment = $experiment_id
                    GROUP BY v.ethnic_group";
$nationality_query = "SELECT n.english AS nationality, COUNT(ve.ID_victim) AS count_nationality
                      FROM nmv__victim_experiment ve
                      LEFT JOIN nmv__victim v ON v.ID_victim = ve.ID_victim
                      LEFT JOIN nmv__nationality n ON n.ID_nationality = v.nationality_1938
                      WHERE ve.ID_experiment = $experiment_id
                      GROUP BY v.nationality_1938";
$compensation_query = "SELECT evc.english as compensation, COUNT(ve.ID_victim) AS count_compensation
                      FROM nmv__victim_experiment ve
                      LEFT JOIN nmv__victim v ON v.ID_victim = ve.ID_victim
                      LEFT JOIN nmv__evaluation ev ON ev.ID_victim = v.ID_victim
                      LEFT JOIN nmv__victim_evaluation_compensation evc ON evc.ID_compensation = ev.compensation
                      WHERE ve.ID_experiment = $experiment_id
                      GROUP BY ev.compensation";
// get number of victims with compensation=-1 in nmv__victim -- This is old information that should be transferred to nmv__evaluation.
// Aleksandra and Paul are doing it
//TODO: delete, when datatransfer to nmv__evaluation is complete (Paul and Aleksandra)
$compensation_outdated_query = "SELECT COUNT(v.compensation)
                                FROM nmv__victim_experiment ve
                                LEFT JOIN nmv__victim v ON v.ID_victim = ve.ID_victim
                                WHERE ve.ID_experiment = $experiment_id AND v.compensation = -1
                                GROUP BY v.compensation";
$compensation_outdated_data = $dbi->connection->query($compensation_outdated_query)->fetch_array();
$compensation_outdated_count = $compensation_outdated_data[0];


//show the data: build tables
// buildTableFromQuery(query,columns,table header,style)
$survival_table = buildTableFromQuery(
    $survival_query,
    ['{survival}', '{count_survival}'],
    ['Survival', 'Number of victims'],
    'grid');
$ethnicity_table = buildTableFromQuery(
    $ethnicity_query,
    ['{ethnic_group}', '{count_ethnic_group}'],
    ['Ethnic Group', 'Number of victims'],
    'grid');
$nationality_table = buildTableFromQuery(
    $nationality_query,
    ['{nationality}', '{count_nationality}'],
    ['Nationality (1938)', 'Number of victims'],
    'grid');
$compensation_table = buildTableFromQuery(
    $compensation_query,
    ['(Compensation Scheme x)', '(Number of victims compensated)'],
    //TODO: activate following line and delet line above when data is transferred
    //['{compensation}', '{count_compensation}'],
    ['Compensation', 'Number of victims'],
    'grid');




$layout
	->set('title','Statistic - Victims of Experiment');
$layout
	->set('content',
          '<br><div>Mouseclick on dropdown-bar and type ID of experiment or click on title of experiment<br>
                    Then click OK-button <br> <br>'
            . $experiment_select->run() .
          '<hr><hr><br><strong>Experiment ID  ' . $experiment_id . ' - ' . $experiment_title . '</strong><br>
          Total number of victims: <strong>' . $total_number . '</strong><br>
          Number of victims compensated: <strong>' . $compensation_outdated_count . '</strong><br><br>

          </div><div>'
            . $survival_table . '<br>'
            . $ethnicity_table . '<br>'
            . $nationality_table . '<br><p>New field for compensation was introduced in the database. Data is not transfered yet. <br>In the future the following table will show the different numbers of victims who got compensation from different compensation schemes. </p>'
            . $compensation_table . '<br>'
            .
          '</div>'



        );
require_once 'statistics_navigation.php'; // navigation to different statistics
$layout->cast();

?>
