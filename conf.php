<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

setlocale(LC_ALL, 'es_ES.UTF8');

function debug($array) {
	echo '<pre>';
	print_r($array);
	echo '</pre>';
}

function to_time($seconds) {
	$mins = floor($seconds / 60.0);
	$sec = $seconds % 60.0;
	return $mins . ':' . zerofill(number_format($sec, 0));
}

function zerofill($num) {
	if ($num < 10) {
		return '0' . $num;
	} else {
		return $num;
	}
}
