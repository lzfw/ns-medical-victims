<?php
/**
* exports table (.csv) for institution queries
*
*
*
*/
function filterData(&$str){
    $str = preg_replace("/\t/", "\\t", $str);
    $str = preg_replace("/\r?\n/", "\\n", $str);
    $str = str_replace("\\n", "---------------", $str);
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
}

require_once 'zefiro/ini.php';
require_once 'nmv_export_queries.php';
$entity = getUrlParameter('entity');

$dbi->requireUserPermission ('view');

$export_query = '';
$export_query_where = '';
$export_query_start = '';
$export_query_end = '';

// Create query for export via sql VIEW
// $export_query_select = "SELECT *
//                         FROM view_$entity";

switch ($entity) {
  case 'victim':
    $export_query_start = $victim_query_start;
    $export_query_end = $victim_query_end;
    break;
  case 'prisoner_assistant':
    $export_query_start = $was_prisoner_assistant_query_start;
    $export_query_end = $prisoner_assistant_query_end;
    break;
  case 'experiment':
    $export_query_start = $experiment_query_start;
    $export_query_end = $experiment_query_end;
    break;
  case 'perpetrator':
    $export_query_start = $perpetrator_query_start;
    $export_query_end = $perpetrator_query_end;
    break;
  case 'institution':
    $export_query_start = $institution_query_start;
    $export_query_end = $institution_query_end;
    break;
  case 'literature':
    $export_query_start = $literature_query_start;
    $export_query_end = $literature_query_end;
    break;
  case 'source':
    $export_query_start = $source_query_start;
    $export_query_end = $source_query_end;
    break;
}



// $export_query_where = " WHERE was_prisoner_assistant != 'prisoner assistant only' AND (TRIM(v.surname) LIKE '%Adler%' OR TRIM(vn.victim_name) LIKE '%Adler%')";
if (getUrlParameter('where-clause')) {
  $export_query_where = getUrlParameter('where-clause');
  $export_query_where = utf8_decode($export_query_where);
  $export_query_where = htmlspecialchars($export_query_where);
  } else {
  // $export_query_where = 'WHERE 1';
  $export_query_where = ' ';
};
$export_query = $export_query_start . ' ' . $export_query_where . ' ' . $export_query_end;
//Obacht
//echo $export_query;
// Fetch records from database
$query_items = $dbi->connection->query($export_query);


if(getUrlParameter('type') == 'csv') {
      // Create csv-file with records
      if($query_items->num_rows > 0) {
          // Csv field separator
          $delimiter = ",";
          // Csv file name for download
          $filename = $entity . "_" . date('Y-m-d') . ".csv";

          // Create a file pointer
          $file = fopen('php://memory', 'w');

          // Create columns and, format as csv and  write to file
          while($row = $query_items->fetch_assoc()) {
            $fields = array();
            if(empty($fields)) {
              $fields = array_keys($row);
            }
          }
          fputcsv($file, $fields, $delimiter);

          // Output each row of the data, format line as csv and write to file pointer
          $query_items = $dbi->connection->query($export_query);
          while($row = $query_items->fetch_assoc()) {
              $lineData = array();

              // Set rows
              foreach($fields as $field) {
                $lineData[] = $row[$field];
              }
              fputcsv($file, $lineData, $delimiter);
          }

          // Move back to beginning of file
          fseek($file, 0);

          // Set headers to download file rather than displayed
          header("Content-Description: File Transfer");

          header('Content-Type: text/csv');
          header('Content-Disposition: attachment; filename="' . $filename . '";');

          //Output all remaining data on a file pointer
          fpassthru($file);
      }
} elseif(getUrlParameter('type') == 'xls') {
      // xls-file with records
      // Excel file name for download
      $fileName = $entity . "_" . date('Y-m-d') . ".xls";

      // Display column names as first row
      while($row = $query_items->fetch_assoc()) {
        $fields = array();
        if(empty($fields)) {
          $fields = array_keys($row);
        }
      }
      $excelData = implode("\t", array_values($fields)) . "\n";

      // Output each row of the data
      $query_items = $dbi->connection->query($export_query);
      if($query_items->num_rows > 0) {
          while($row = $query_items->fetch_assoc()) {
              $lineData = array();
              foreach($fields as $field) {
                $lineData[] = $row[$field];
              }
              array_walk($lineData, 'filterData');
              $excelData .= implode("\t", array_values($lineData)) . "\n";
          }
      }else{
          $excelData .= 'No records found...'. "\n";
      }
      // Headers for download
      header("Content-Type: application/vnd.ms-excel; charset=utf-8");
      // header("Content-Type: application/vnd.ms-excel");
      header("Content-Disposition: attachment; filename=\"$fileName\"");

      // Render excel data
      echo chr(255) . chr(254) . mb_convert_encoding($excelData, 'UTF-16LE', 'UTF-8');
}
exit;

?>
