<?php

function list_system_locales(){
	ob_start();
	system('locale -a');
	$str = ob_get_contents();
	ob_end_clean();
	return split("\\n", trim($str));
}

$locales = list_system_locales();

print_r($locales);

?>
