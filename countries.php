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
?>
<?php
foreach($ga->getResults() as $result) {
	?>
	<tr class="countries_<?php echo $profile_id ?>">
		<td class="first"><?php echo $result ?></td>
		<td><?php echo $result->getVisits() ?></td>
		<td><?php echo $result->getVisitors() ?></td>
		<td><?php echo $result->getPageviews() ?></td>
		<td><?php echo number_format($result->getPageviews() / $result->getVisits(), 2) ?></td>
		<td><?php echo to_time($result->getAvgTimeOnSite()) ?></td>
		<td><?php echo number_format($result->getVisitBounceRate(), 2) ?></td>
		<td><?php echo $result->getUniquePageviews() ?></td>
		<td><?php echo $result->getNewVisits() ?></td>
	</tr>
	<?php
}
