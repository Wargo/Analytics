<?php
echo '
<div class="new_form pull-right">
	<form class="form-inline" method="POST">
		<input type="hidden" name="queryType" value="' . $queryType . '" />';

		echo '<select name="field" class="input-large clearfix">';
			foreach ($_fields as $key => $value) {
				if ($_POST['field'] == $key) {
					echo '<option selected="selected" value="' . $key . '">' . $value . '</option>';
				} else {
					echo '<option value="' . $key . '">' . $value . '</option>';
				}
			}
		echo '</select>

		<select name="time" class="input-large clearfix">
			<option value="daily">Diariamente</option>
			<option disabled="disabled" value="weekly">Semanalmente</option>
			<option value="monthly">Mensualmente</option>
			<option disabled="disabled" value="yearly">Anualmente</option>
		</select>';

		echo '
		<input type="text" name="date_start" class="input-small" id="dp1" value="' . $date_start . '" />
		<input type="text" name="date_end" class="input-small" id="dp2" value="' . $date_end . '" />';
		foreach($profiles_id as $profile_id) {
			echo '<input type="hidden" name="profile_id[]" value="' . $profile_id. '" />';
		}
		echo ' <input type="submit" class="btn btn-primary calculate2" count="' . count($profiles_id) . '" multiplier="4" value="Calcular" />
	</form>
</div>
';

$chart = '[\'Fecha\',';

foreach($profiles_id as $profile_id) {
	$chart .= '\'' . $_SESSION['accounts'][$profile_id] . '\',';
}

$chart = substr($chart, 0, -1) . '],
';
$values = $dates = array();

switch ($time) {
	case 'daily':
		$start = $date_start;
		$end = $date_end;
		$segments[] = array('start' => $start, 'end' => $start);
		for ($i = 1; $i <= 1000; $i ++) {
			$aux = date('Y-m-d', mktime(0, 0, 0, substr($date_start, 5, 2), substr($date_start, 8, 2) + $i, substr($date_start, 0, 4)));
			$segments[] = array('start' => $aux, 'end' => $aux);
			if ($aux == $end) {
				break;
			}
		}
		break;
	case 'monthly':
		$start = date('Y-m-d', mktime(0, 0, 0, substr($date_start, 5, 2), 1, substr($date_start, 0, 4)));
		$end = date('Y-m-d', mktime(0, 0, 0, substr($date_end, 5, 2), 1, substr($date_end, 0, 4)));

		$start2 = date('Y-m-d', mktime(0, 0, 0, substr($date_start, 5, 2) + 1, 0, substr($date_start, 0, 4)));

		$segments[] = array('start' => $start, 'end' => $start2);

		for ($i = 1; $i <= 100; $i ++) {
			$aux = date('Y-m-d', mktime(0, 0, 0, substr($date_start, 5, 2) + $i, 1, substr($date_start, 0, 4)));
			$aux2 = date('Y-m-d', mktime(0, 0, 0, substr($date_start, 5, 2) + $i + 1, 0, substr($date_start, 0, 4)));
			$segments[] = array('start' => $aux, 'end' => $aux2);
			if ($aux == $end) {
				break;
			}
		}
		break;
}

if ($field != 'pageVisitor') {
	$aux_field = array($field);
	$field_order = $field;
} else {
	$aux_field = array('uniquePageviews', 'visits');
	$field_order = 'visits';
}

echo '<a class="pull-left btn btn-inverse" href="index.php">Volver</a>';
?>
<table class="table">
	<thead>
		<tr>
			<th>Web</th>
			<?php
			foreach ($segments as $segment) {
				switch ($time) {
					case 'daily':
						echo '<th>' . strftime('%A %d', strtotime($segment['start'])) . '</th>';
						$dates[] = strftime('%A %d', strtotime($segment['start']));
						break;
					case 'monthly':
						echo '<th>' . strftime('%B', strtotime($segment['start'])) . '</th>';
						$dates[] = strftime('%B', strtotime($segment['start']));
						break;
				}
			}
			?>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ($profiles_id as $profile_id) {
			$filter = '';
			$limit = 25;
			$offset = 1;
			?>
			<tr>
				<td class="first"><?php echo $_SESSION['accounts'][$profile_id]; ?></td>
				<?php
				$count = 0;
				foreach ($segments as $segment) {
					$count ++;
					$total = 'total_' . $count;
					if (empty(${$total})) {
						${$total} = 0;
					}
					try {
						$ga->requestReportData(
							$profile_id,
							array('country'), // campos (agrupar por...)
							$aux_field,
							'-' . $field_order, // orden
							$filter, // condiciones
							$segment['start'], // fecha inicio
							$segment['end'], // fecha final
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
					switch ($field) {
						case 'visits':
							echo '<td>' . $ga->getVisits() . '</td>';
							${$total} += $values[$count - 1][] = $ga->getVisits();
							break;
						case 'visitors':
							echo '<td>' . $ga->getVisitors() . '</td>';
							${$total} += $values[$count - 1][] = $ga->getVisitors();
							break;
						case 'pageviews':
							echo '<td>' . $ga->getPageviews() . '</td>';
							${$total} += $values[$count - 1][] = $ga->getPageviews();
							break;
						case 'pageVisitor':
							echo '<td>' . number_format($ga->getUniquePageviews() / $ga->getVisits(), 2) . '</td>';
							if ($ga->getVisits()) {
								${$total} += $values[$count - 1][] = number_format($ga->getUniquePageviews() / $ga->getVisits(), 2);
							} else {
								${$total} += 0;
							}
							break;
						case 'avgTimeOnSite':
							echo '<td>' . to_time($ga->getAvgTimeOnSite()) . '</td>';
							${$total} += $values[$count - 1][] = $ga->getAvgTimeOnSite();
							break;
						case 'visitBounceRate':
							echo '<td>' . number_format($ga->getVisitBounceRate(), 2) . '</td>';
							${$total} += $values[$count - 1][] = number_format($ga->getVisitBounceRate(), 2);
							break;
						case 'uniquePageviews':
							echo '<td>' . $ga->getUniquePageviews() . '</td>';
							${$total} += $values[$count - 1][] = $ga->getUniquePageviews();
							break;
						case 'newVisits':
							echo '<td>' . $ga->getNewVisits() . '</td>';
							${$total} += $values[$count - 1][] = $ga->getNewVisits();
							break;
					}
				}
				?>
			</tr>
			<?php
		}
		?>
	</tbody>
	<tfoot>
		<tr class="total">
			<td class="first">TOTAL / MEDIA</td>
			<?php
			$count = 0;
			foreach ($segments as $segment) {
				$count ++;
				$total = 'total_' . $count;
				if ($field == 'avgTimeOnSite') {
					echo '<td>' . to_time(${$total} / count($profiles_id)) . '</td>';
				} elseif($field == 'pageVisitor' || $field == 'visitBounceRate') {
					echo '<td>' . number_format(${$total} / count($profiles_id), 2) . '</td>';
				} else {
					echo '<td>' . ${$total} . '</td>';
				}
			}
			?>
		</tr>
	</tfoot>
</table>

<?php
foreach ($dates as $key => $value) {
	$chart .= '[\'' . $value . '\',';
	foreach ($values[$key] as $v) {
		$chart .= $v . ',';
	}
	$chart = substr($chart, 0, -1);
	$chart .= '],
';
}

$chart = substr($chart, 0, -1);
?>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
	google.load("visualization", "1", {packages:["corechart"]});
	google.setOnLoadCallback(drawChart);
	function drawChart() {
		var data = google.visualization.arrayToDataTable([
			<?php
			echo $chart;
			?>
			//['Fecha', 'web1', 'web2'],
			//['2004', 1000, 400],
			//['2005', 1170, 460],
			//['2006', 660, 920],
			//['2007', 1030, 540]
		]);

		var options = {
			title: '<?php echo $_fields[$_POST['field']]; ?>'
			//hAxis: {title: 'Fecha',  titleTextStyle: {color: 'red'}}
		};

		//var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
		var chart = new google.visualization.LineChart(document.getElementById('chart_div_line'));
		chart.draw(data, options);
		var chart = new google.visualization.AreaChart(document.getElementById('chart_div_area'));
		chart.draw(data, options);
		var chart = new google.visualization.ColumnChart(document.getElementById('chart_div_column'));
		chart.draw(data, options);

		var options = {
			title: '<?php echo $_fields[$_POST['field']]; ?> sólo de la primera web',
			is3D:true
		};
		var chart = new google.visualization.PieChart(document.getElementById('chart_div_pie'));
		chart.draw(data, options);
	}
</script>
<div id="chart_div_line" style="width: 100%; height: 500px;"></div>
<div id="chart_div_column" style="width: 100%; height: 500px;"></div>
<div id="chart_div_area" style="width: 100%; height: 500px;"></div>
<div id="chart_div_pie" style="width: 100%; height: 500px;"></div>
