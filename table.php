<?php
echo '<h3 class="change-date pull-right">Fecha: ' . strftime('%A %e %b %Y', strtotime($date_start)) . ' - ' . strftime('%A %e %b %Y', strtotime($date_end)) . '</h3>';
echo '
<div class="new_form hidden pull-right">
	<form class="form-inline" method="POST">
		<input type="text" name="date_start" class="input-small" id="dp1" value="' . $date_start . '" />
		<input type="text" name="date_end" class="input-small" id="dp2" value="' . $date_end . '" />';
		foreach($profiles_id as $profile_id) {
			echo '<input type="hidden" name="profile_id[]" value="' . $profile_id. '" />';
		}
		echo ' <input type="submit" class="btn btn-primary" value="Calcular" />
	</form>
</div>
';
echo '<a class="pull-left btn btn-inverse" href="javascript:history.go(-1);">Volver</a>';
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
