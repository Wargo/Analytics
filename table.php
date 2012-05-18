<?php
if ($comparing) {
	$hidden = '';
} else {
	$hidden = 'hidden';
}

echo '
<div class="new_form pull-right">
	<form class="form-inline" method="POST">';
		echo '<a href="#" class="compare' . ($comparing?' hidden':'') . '">Añadir fecha comparativa</a>';
		echo '<div class="compare_fields ' . $hidden . ' pull-left">
			<i title="Eliminar fecha comparativa" class="remove_compare icon-ban-circle"></i>
			<input ' . ($comparing?'':'disabled="disabled"') . ' type="text" name="date_start_comp" class="input-small" id="dp3" value="' . $date_start_comp . '" />
			<input ' . ($comparing?'':'disabled="disabled"') . ' type="text" name="date_end_comp" class="input-small" id="dp4" value="' . $date_end_comp . '" />
			-&nbsp;
		</div>
		<input type="hidden" name="queryType" value="' . $queryType . '" />
		<input type="text" name="date_start" class="input-small" id="dp1" value="' . $date_start . '" />
		<input type="text" name="date_end" class="input-small" id="dp2" value="' . $date_end . '" />';
		foreach($profiles_id as $profile_id) {
			echo '<input type="hidden" name="profile_id[]" value="' . $profile_id. '" />';
		}
		echo ' <input type="submit" class="btn btn-primary" value="Calcular" />
	</form>
</div>
';
echo '<a class="pull-left btn btn-inverse" href="index.php">Volver</a>';
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
		if ($comparing) {
			$_total = $_total_visits = $_total_visitors = $_total_page_views = $_total_page_per_visit = $_total_time = $_total_bounce = $_total_unique_page_views = $_total_new_visits = 0;
		}
		foreach ($profiles_id as $profile_id) {
			$filter = '';
			$limit = 25;
			$offset = 1;

			try {
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
			} catch(Exception $e) {
				$_SESSION['error'] = array(
					'title' => 'Error!',
					'content' => $e->getMessage(),
				);
				echo '<meta http-equiv="refresh" content="0; url=index.php">';
				die;
			}

			if ($comparing) {
				$_ga = new gapi($_SESSION['user'], $_SESSION['pass']);
				$_ga->requestReportData(
					$profile_id,
					array('country'), // campos (agrupar por...)
					array('pageviews', 'visits', 'uniquePageviews', 'newVisits', 'timeOnSite', 'visitors', 'visitBounceRate', 'avgTimeOnSite'), // ,'bounce', 'entranceBounceRate'), // datos
					'-visits', // orden
					$filter, // condiciones
					$date_start_comp, // fecha inicio
					$date_end_comp, // fecha final
					$offset, // desde
					$limit // límite
				);

				$_total_visits += $_ga->getVisits();
				$_total_visitors += $_ga->getVisitors();
				$_total_page_views += $_ga->getPageviews();
				if ($_ga->getVisits()) {
					$_total_page_per_visit += $_ga->getPageviews() / $_ga->getVisits();
				} else {
					$_total_page_per_visit += 0;
				}
				$_total_time += $_ga->getAvgTimeOnSite();
				$_total_bounce += $_ga->getVisitBounceRate();
				$_total_unique_page_views += $_ga->getUniquePageviews();
				$_total_new_visits += $_ga->getNewVisits();
			}

			if ($ga->getVisits()) {
				$total ++;
			}
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
				if ($comparing) {
					echo '<td class="first"><a var="' . $profile_id . '" class="country show_country" href="countries.php?profile_id='.$profile_id.'&date_start='.$date_start.'&date_end='.$date_end.'&date_start_comp='.$date_start_comp.'&date_end_comp='.$date_end_comp.'">' . $_SESSION['accounts'][$profile_id] . '</a></td>';
				} else {
					echo '<td class="first"><a var="' . $profile_id . '" class="country show_country" href="countries.php?profile_id='.$profile_id.'&date_start='.$date_start.'&date_end='.$date_end.'">' . $_SESSION['accounts'][$profile_id] . '</a></td>';
				}
				echo '<td var="' . ($comparing?$_ga->getVisits():'') . '">' . $ga->getVisits() . '</td>';
				echo '<td var="' . ($comparing?$_ga->getVisitors():'') . '">' . $ga->getVisitors() . '</td>';
				echo '<td var="' . ($comparing?$_ga->getPageviews():'') . '">' . $ga->getPageviews() . '</td>';
				if ($ga->getVisits()) {
					if ($comparing && $_ga->getVisits()) {
						echo '<td var="' . ($comparing?number_format($_ga->getPageviews() / $_ga->getVisits(), 2):'') . '">' . number_format($ga->getPageviews() / $ga->getVisits(), 2) . '</td>';
					} else {
						if ($comparing) {
							echo '<td var="0">' . number_format($ga->getPageviews() / $ga->getVisits(), 2) . '</td>';
						} else {
							echo '<td>' . number_format($ga->getPageviews() / $ga->getVisits(), 2) . '</td>';
						}
					}
				} else {
					if ($comparing && $_ga->getVisits()) {
						echo '<td var="' . ($comparing?number_format($_ga->getPageviews() / $_ga->getVisits(), 2):'') . '">0</td>';
					} else {
						if ($comparing) {
							echo '<td var="0">0</td>';
						} else {
							echo '<td>0</td>';
						}
					}
				}
				echo '<td class="is_time" var="' . ($comparing?to_time($_ga->getAvgTimeOnSite()):'') . '" title="' . ($comparing?diff($ga->getAvgTimeOnSite(), $_ga->getAvgTimeOnSite()):'') . '">' . to_time($ga->getAvgTimeOnSite()) . '</td>';
				echo '<td class="inverse" var="' . ($comparing?number_format($_ga->getVisitBounceRate(), 2):'') . '">' . number_format($ga->getVisitBounceRate(), 2) . ' %</td>';
				echo '<td var="' . ($comparing?$_ga->getUniquePageviews():'') . '">' . $ga->getUniquePageviews() . '</td>';
				echo '<td var="' . ($comparing?$_ga->getNewVisits():'') . '">' . $ga->getNewVisits() . '</td>';
			echo '</tr>';
		}
	echo '</tbody>';
	echo '<tfoot>';
		echo '<tr class="total">';
			echo '<td class="first">TOTAL / MEDIA</td>';
			echo '<td var="' . ($comparing?$_total_visits:'') . '">' . $total_visits . '</td>';
			echo '<td var="' . ($comparing?$_total_visitors:'') . '">' . $total_visitors . '</td>';
			echo '<td var="' . ($comparing?$_total_page_views:'') . '">' . $total_page_views . '</td>';
			echo '<td var="' . ($comparing?number_format($_total_page_per_visit / $total, 2):'') . '">' . number_format($total_page_per_visit / $total, 2) . '</td>';
			echo '<td class="is_time" var="' . ($comparing?to_time($_total_time / $total):'') . '" title="' . ($comparing?diff($total_time, $_total_time, $total):'') . '">' . to_time($total_time / $total) . '</td>';
			echo '<td class="inverse" var="' . ($comparing?number_format($_total_bounce / $total, 2):'') . '">' . number_format($total_bounce / $total, 2) . ' %</td>';
			echo '<td var="' . ($comparing?$_total_unique_page_views:'') . '">' . $total_unique_page_views . '</td>';
			echo '<td var="' . ($comparing?$_total_new_visits:'') . '">' . $total_new_visits . '</td>';
		echo '</tr>';
	echo '</tfoot>';
echo '</table>';
