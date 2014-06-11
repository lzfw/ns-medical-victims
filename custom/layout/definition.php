<?php

$layout = new Layout ();

$layout->declareString ('title');
$layout->declareBlock ('content');
$layout->declareBlock ('sidebar');

$layout->setTemplate ('custom/layout/switch.php');

?>