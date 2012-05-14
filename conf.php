<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

setlocale(LC_ALL, 'es_ES.UTF8');

define('ga_email','guillermo@artvisual.net');
define('ga_password','will2805');

function debug($array) {
	echo '<pre>';
	print_r($array);
	echo '</pre>';
}

function to_time($seconds) {
	if ($seconds >= 60) {
		$mins = floor($seconds / 60);
		$sec = ($seconds / 60) - floor($seconds / 60);
		$sec = $sec * 60;
		return $mins . ':' . zerofill(number_format($sec, 0));
	} else {
		return '0:' . number_format($seconds, 0);
	}
}

function zerofill($num) {
	if ($num < 10) {
		return '0' . $num;
	} else {
		return $num;
	}
}
