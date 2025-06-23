<?php

function createButton () {
	$args = func_get_args();
	// 0: text
	// 1: link
	// 2: class
	// 3: title
	// 4: onclick
	return
		(isset($args[1])?'<a href="'.$args[1].'">':'')
		.'<span'
		.' class="button'.(isset($args[2])?' '.$args[2]:'').'"'
		.(isset($args[4])?' onclick="'.$args[4].'"':'')
		.(isset($args[3])?' title="'.$args[3].'"':'')
		.'>'
		.(isset($args[0])?$args[0]:'')
		.'</span>'
		.(isset($args[1])?'</a>':'').PHP_EOL;
}

function createBackButton () {
	return createButton(L_BACK,'javascript:history.back()','icon back');
}

function createSmallButton () {
	$args = func_get_args();
	// 0: text
	// 1: link
	// 2: class
	// 3: title
	// 4: onclick
	return
		(isset($args[1])?'<a href="'.$args[1].'">':'')
		.'<span'
		.' class="smallbutton'.(isset($args[2])?' '.$args[2]:'').'"'
		.(isset($args[4])?' onclick="'.$args[4].'"':'')
		.(isset($args[3])?' title="'.$args[3].'"':'')
		.'>'
		.(isset($args[0])?$args[0]:'')
		.'</span>'
		.(isset($args[1])?'</a>':'').PHP_EOL;
}

function createListItem () {
	$args = func_get_args();
	// 0: text
	// 1: link
	// 2: class
	return
		'<li'.(isset($args[2])?' class="'.$args[2].'"':'').'><a href="'.$args[1].'">'
		.(isset($args[0])?$args[0]:'').'</a></li>';
}

function createIcon () {
	$args = func_get_args();
	// 0: image
	// 1: title
	return
		'<img'
		.(isset($args[0])?' src="'.$args[0].'"':'')
		.(isset($args[1])?' title="'.$args[1].'"':'')
		.'>';
}

?>
