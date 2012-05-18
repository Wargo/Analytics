<?php
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
				echo '<h2>Listado de proyectos</h2>';
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
					echo '<select name="queryType" class="input-large clearfix queryType">';
						echo '<option value="1">General</option>';
						echo '<option value="2" disabled="disabled">Por bloque temporal</option>';
					echo '</select>';
					echo '<div class="more_options hidden">';
						echo '<select name="field" class="input-large clearfix">';
							echo '<option value="visits">Visitas</option>';
							echo '<option value="visitors">Visitantes</option>';
							echo '<option value="pageViews">Páginas vistas</option>';
							echo '<option value="pageVisitor">Páginas por visita</option>';
							echo '<option value="time">Tiempo medio</option>';
							echo '<option value="bounce">% Rebote</option>';
							echo '<option value="uniqueViews">Páginas únicas vitas</option>';
							echo '<option value="newVisitors">Nuevos visitantes</option>';
						echo '</select>';
						echo '<select name="time" class="input-large clearfix">';
							echo '<option value="daily">Diariamente</option>';
							echo '<option value="weekly">Semanalmente</option>';
							echo '<option value="monthly">Mensualmente</option>';
							echo '<option value="yearly">Anualmente</option>';
						echo '</select>';
					echo '</div>';
					echo '<button class="btn btn-primary calculate" type="submit">Calcular</button> ';
					echo '<button class="btn btn-danger btn-mini remove_checks">Quitar todos los checks</button>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</form>';
echo '</div>';
