<?php
session_start();
include 'conf.php';
require 'gapi.class.php';
$ga = new gapi(ga_email,ga_password);
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
			echo '<h3 class="change-date pull-right">Fecha: ' . strftime('%A %e %b %Y', strtotime($date_start)) . ' - ' . strftime('%A %e %b %Y', strtotime($date_end)) . '</h3>';
			echo '
			<div class="new_form hidden pull-right">
				<form class="form-inline" method="POST">
					<input type="text" name="date_start" class="input-small" id="dp1" value="' . $date_start . '" />
					<input type="text" name="date_end" class="input-small" id="dp2" value="' . $date_end . '" />';
					foreach($profiles_id as $profile_id) {
						echo '<input type="hidden" name="profile_id[]" value="' . $profile_id. '" />';
					}
					echo '<input type="submit" class="btn btn-primary" value="Calcular" />
				</form>
			</div>
			';
			echo '<a class="pull-left btn btn-inverse" href="/">Volver</a>';
			echo '
			<table class="table">
				<thead>
					<tr>
						<th>Web</th>
						<th>Visitas</th>
						<th>Visitantes</th>
						<th>Páginas vistas</th>
						<th>Páginas por visita</th>
						<th>Tiempo medio</th>
						<th>% Rebote</th>
						<th>Páginas únicas vistas</th>
						<th>Nuevos visitantes</th>
					</tr>
				</thead>
				<tbody>';
					$total = $total_visits = $total_visitors = $total_page_views = $total_page_per_visit = $total_time = $total_bounce = $total_unique_page_views = $total_new_visits = 0;
					foreach ($profiles_id as $profile_id) {
						$total ++;
						$filter = '';
						$limit = 25;
						$offset = 1;

						$ga->requestReportData(
							$profile_id,
							array('country'), // campos (agrupar por...)
							array('pageviews', 'visits', 'uniquePageviews', 'newVisits', 'timeOnSite', 'visitors', 'visitBounceRate', 'avgTimeOnSite'), // ,'bounce', 'entranceBounceRate'), // datos
							'-visits', // orden
							$filter, // condiciones
							$date_start, // fecha inicio
							$date_end, // fecha final
							$offset, // desde
							$limit // límite
						);

						$total_visits += $ga->getVisits();
						$total_visitors += $ga->getVisitors();
						$total_page_views += $ga->getPageviews();
						if ($ga->getVisits()) {
							$total_page_per_visit += $ga->getPageviews() / $ga->getVisits();
						} else {
							$total_page_per_visit += 0;
						}
						$total_time += $ga->getAvgTimeOnSite();
						$total_bounce += $ga->getVisitBounceRate();
						$total_unique_page_views += $ga->getUniquePageviews();
						$total_new_visits += $ga->getNewVisits();

						echo '<tr>';
							echo '<td class="first">' . $_SESSION['accounts'][$profile_id] . '</td>';
							echo '<td>' . number_format($ga->getVisits(), 0, '.', '') . '</td>';
							echo '<td>' . number_format($ga->getVisitors(), 0, '.', '') . '</td>';
							echo '<td>' . number_format($ga->getPageviews(), 0, '.', '') . '</td>';
							if ($ga->getVisits()) {
								echo '<td>' . number_format($ga->getPageviews() / $ga->getVisits(), 2, '.', '') . '</td>';
							} else {
								echo '<td>0</td>';
							}
							echo '<td>' . to_time($ga->getAvgTimeOnSite()) . '</td>';
							echo '<td>' . number_format($ga->getVisitBounceRate(), 2, '.', '') . ' %</td>';
							echo '<td>' . number_format($ga->getUniquePageviews(), 0, '.', '') . '</td>';
							echo '<td>' . number_format($ga->getNewVisits(), 0, '.', '') . '</td>';
						echo '</tr>';
					}
				echo '</tbody>';
				echo '<tfoot>';
					echo '<tr class="total">';
						echo '<td class="first">TOTAL / MEDIA</td>';
						echo '<td>' . number_format($total_visits, 0, '.', '') . '</td>';
						echo '<td>' . number_format($total_visitors, 0, '.', '') . '</td>';
						echo '<td>' . number_format($total_page_views, 0, '.', '') . '</td>';
						echo '<td>' . number_format($total_page_per_visit / $total, 2, '.', '') . '</td>';
						echo '<td>' . to_time($total_time / $total) . '</td>';
						echo '<td>' . number_format($total_bounce / $total, 2, '.', '') . ' %</td>';
						echo '<td>' . number_format($total_unique_page_views, 0, '.', '') . '</td>';
						echo '<td>' . number_format($total_new_visits, 0, '.', '') . '</td>';
					echo '</tr>';
				echo '</tfoot>';
			echo '</table>';
		} else {
			$ga->requestAccountData(1, 1000);
			$results = $ga->getResults();
			$aux = array();
			$i = 0;
			foreach ($results as $result) {
				$aux[$i]['title'] = $result->getTitle();
				$aux[$i]['profileId'] = $result->getProfileId();
				$aux[$i]['accountId'] = $result->getAccountId();
				$aux[$i]['accountName'] = $result->getAccountName();
				$i ++;
				$_SESSION['accounts'][$result->getProfileId()] = $result->getTitle();
			}

			$accounts = array();
			foreach ($aux as $a) {
				$accounts[$a['accountId']]['accountName'] = $a['accountName'];
				$accounts[$a['accountId']]['webs'][] = array(
					'profileId' => $a['profileId'],
					'title' => $a['title'],
					'accountId' => $a['accountId']
				);
			}
			echo '<div class="container-fluid">';
				echo '<form method="POST" action="" class="well">';
					echo '<div class="row-fluid">';
						echo '<div class="span9">';
							echo '<ul>';
								foreach($accounts as $key => $value) {
									echo '<li>';
										echo '<div class="more-less">+</div>';
										echo '<label for="' . $key . '" class="checkbox">';
										echo '<input class="account" type="checkbox" name="account_id[]" value="' . $key . '" id="' . $key . '" />';
										echo $value['accountName']. '</label>';
									echo '</li>';
									if (count($value['webs'])) {
										echo '<ul class="hidden">';
											foreach ($value['webs'] as $web) {
												echo '<li>';
													echo '<label for="' . $web['profileId'] . '" class="checkbox">';
													echo '<input class="account_' . $key . '" type="checkbox" name="profile_id[]" value="' . $web['profileId'] . '" id="' . $web['profileId'] . '" />';
													echo $web['title']. '</label>';
												echo '</li>';
											}
										echo '</ul>';
									}
								}
							echo '</ul>';
						echo '</div>';
						echo '<div class="span2">';
							echo '<div class="fixed">';
								$date_start = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 8 , date('Y')));
								$date_end = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 1 , date('Y')));
								echo '<input value="' . $date_start . '" class="input-large" type="text" name="date_start" placeholder="fecha inicio" id="dp1" />';
								echo '<input value="' . $date_end . '" class="input-large" type="text" name="date_end" placeholder="fecha fin" id="dp2" />';
								echo '<select name="queryType" class="input-large clearfix">';
									echo '<option value="1">General</option>';
									echo '<option disabled="disabled" value="2">Por bloque temporal</option>';
								echo '</select>';
								echo '<button class="btn btn-primary calculate" type="submit">Calcular</button> ';
								echo '<button class="btn btn-danger btn-mini remove_checks">Quitar todos los checks</button>';
							echo '</div>';
						echo '</div>';
					echo '</div>';
				echo '</form>';
			echo '</div>';
		}
		?>
	</body>
</html>
