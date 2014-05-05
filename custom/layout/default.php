<?php

$layout = new Layout ();

$layout->declareString ('title');
$layout->declareBlock ('body');
$layout->declareBlock ('sidebar');

$layout->setTemplate ('custom/templates/switch.php');

?>