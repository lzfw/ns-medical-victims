<?php

function createButton () {
	$args = func_get_args();
	// 0: text
	// 1: link
	// 2: class
	// 3: title
	// 4: onclick
	return
		(isset($args[1])?'<a href="'.htmlspecialchars((string) $args[1], ENT_QUOTES, 'UTF-8', false).'">':'')
		.'<span'
		.' class="button'.(isset($args[2])?' '.$args[2]:'').'"'
		.(isset($args[4])?' onclick="'.$args[4].'"':'')
		.(isset($args[3])?' title="'.htmlspecialchars((string) $args[3], ENT_QUOTES, 'UTF-8', false).'"':'')
		.'>'
		.(isset($args[0])?safeHTML($args[0]):'')
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
		(isset($args[1])?'<a href="'.htmlspecialchars((string) $args[1], ENT_QUOTES, 'UTF-8', false).'">':'')
		.'<span'
		.' class="smallbutton'.(isset($args[2])?' '.$args[2]:'').'"'
		.(isset($args[4])?' onclick="'.$args[4].'"':'')
		.(isset($args[3])?' title="'.htmlspecialchars((string) $args[3], ENT_QUOTES, 'UTF-8', false).'"':'')
		.'>'
		.(isset($args[0])?safeHTML($args[0]):'')
		.'</span>'
		.(isset($args[1])?'</a>':'').PHP_EOL;
}

function createListItem () {
	$args = func_get_args();
	// 0: text
	// 1: link
	// 2: class
	return
		'<li'.(isset($args[2])?' class="'.$args[2].'"':'').'><a href="'.htmlspecialchars((string) $args[1], ENT_QUOTES, 'UTF-8', false).'">'
		.(isset($args[0])?safeHTML($args[0]):'').'</a></li>';
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
