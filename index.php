<?php
session_start();
include 'conf.php';
require 'gapi.class.php';
if (!empty($_POST['user']) && !empty($_POST['pass'])) {
	$_SESSION['user'] = $_POST['user'];
	$_SESSION['pass'] = $_POST['pass'];
}
if (!empty($_SESSION['user']) && !empty($_SESSION['pass'])) {
	$ga = new gapi($_SESSION['user'], $_SESSION['pass']);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<link type="text/css" rel="stylesheet" href="css.css">
		<link type="text/css" rel="stylesheet" href="bootstrap/docs/assets/css/bootstrap.css">
		<link type="text/css" rel="stylesheet" href="bootstrap/docs/assets/css/datepicker.css">
		<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
		<script src="js.js"></script>
		<script src="jquery.tablesorter.min.js"></script>
		<script src="bootstrap/docs/assets/js/bootstrap-datepicker.js"></script>
		<title>Analytics</title>
	</head>
	<body>
		<?php
		if (!empty($_POST['profile_id'])) {
			$date_start = $_POST['date_start'];
			$date_end = $_POST['date_end'];
			$profiles_id = $_POST['profile_id'];
			if (!empty($_POST['queryType'])) {
				$queryType = $_POST['queryType'];
			} else {
				$queryType = 1;
			}
			if (!empty($_POST['date_start_comp'])) {
				$date_start_comp = $_POST['date_start_comp'];
				$date_end_comp = $_POST['date_end_comp'];
				$comparing = true;
			} else {
				$date_start_comp = $date_start;
				$date_end_comp = $date_end;
				$comparing = false;
			}
			switch ($queryType) {
				case 1:
					include 'table.php';
					break;
				case 2:
					include 'table2.php';
					break;
			}
		} else {
			if (!empty($_SESSION['user']) && !empty($_SESSION['pass'])) {
				include 'list.php';
			} else {
				include 'auth.php';
			}
		}
		?>
	</body>
</html>
