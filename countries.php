<?php
session_start();
include 'conf.php';
require 'gapi.class.php';
if (!empty($_SESSION['user']) && !empty($_SESSION['pass'])) {
	$ga = new gapi($_SESSION['user'], $_SESSION['pass']);
} else {
	return false;
}
$filter = '';
$date_start = $_GET['date_start'];
$date_end = $_GET['date_end'];
$profile_id = $_GET['profile_id'];
$limit = 20;
$offset = 1;

if (!empty($_GET['date_start_comp'])) {
	$date_start_comp = $_GET['date_start_comp'];
	$date_end_comp = $_GET['date_end_comp'];
	$comparing = true;
	$_ga = new gapi($_SESSION['user'], $_SESSION['pass']);
} else {
	$comparing = false;
}


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

if ($comparing) {
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

	$_result = $_ga->getResults();
}

$i = 0;
foreach($ga->getResults() as $result) {
	?>
	<tr class="country_row countries_<?php echo $profile_id ?>">
		<td class="first"><?php echo $result ?></td>
		<td var="<?php if ($comparing) echo $_result[$i]->getVisits(); ?>"><?php echo $result->getVisits(); ?></td>
		<td var="<?php if ($comparing) echo $_result[$i]->getVisitors(); ?>"><?php echo $result->getVisitors() ?></td>
		<td var="<?php if ($comparing) echo $_result[$i]->getPageviews(); ?>"><?php echo $result->getPageviews() ?></td>
		<td var="<?php if ($comparing) echo number_format($_result[$i]->getPageviews() / $_result[$i]->getVisits(), 2); ?>"><?php echo number_format($result->getPageviews() / $result->getVisits(), 2) ?></td>
		<td var="<?php if ($comparing) echo to_time($_result[$i]->getAvgTimeOnSite()); ?>"><?php echo to_time($result->getAvgTimeOnSite()) ?></td>
		<td var="<?php if ($comparing) echo number_format($_result[$i]->getVisitBounceRate(), 2); ?>"><?php echo number_format($result->getVisitBounceRate(), 2) ?> %</td>
		<td var="<?php if ($comparing) echo $_result[$i]->getUniquePageviews(); ?>"><?php echo $result->getUniquePageviews() ?></td>
		<td var="<?php if ($comparing) echo $_result[$i]->getNewVisits(); ?>"><?php echo $result->getNewVisits() ?></td>
	</tr>
	<?php
	$i ++;
}
