<?php
session_start();
include 'conf.php';
require 'gapi.class.php';

if (!empty($_SESSION['user']) && !empty($_SESSION['pass'])) {
	try {
		$ga = new gapi($_SESSION['user'], $_SESSION['pass']);
	} catch(Exception $e) {
		$ga = null;
		$_SESSION['user'] = $_POST['google_email'] = null;
		$_SESSION['pass'] = $_POST['google_password'] = null;
		$_SESSION['error'] = array('title' => 'Error de autentificación!', 'content' => 'El email o la contraseña no son correctos');;
	}
} else {
	$_POST['profile_id'] = null;
}

$profiles_id = $_POST['profile_id'];
$date_start = $_POST['date_start'];
$date_end = $_POST['date_end'];

$queryType = $_POST['queryType'];

$total = $total_visits = $total_visitors = $total_page_views = $total_page_per_visit = $total_time = $total_bounce = $total_unique_page_views = $total_new_visits = 0;

foreach ($profiles_id as $profile_id) {
	$filter = '';
	$limit = 40;
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

	if ($ga->getVisits()) {
		$total ++;
	}
	$total_visits += $ga->getVisits();
	$total_visitors += $ga->getVisitors();
	$total_page_views += $ga->getPageviews();

	if ($queryType == 3) {

		$data[$profile_id] = array(
			'name' => $_SESSION['accounts'][$profile_id],
			'Visitas' => $ga->getVisits(),
			'Visitantes' => $ga->getVisitors(),
			'Páginas vistas' => $ga->getPageviews(),
		);

	} else {

		$filter = '';
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

		$countries = array(array(
			'name' => $_SESSION['accounts'][$profile_id],
			'Visitas' => $ga->getVisits(),
			'Visitantes' => $ga->getVisitors(),
			'Páginas vistas' => $ga->getPageviews(),
		));

		foreach($ga->getResults() as $result) {
			$countries[] = array(
				'Country' => $result,
				'Visitas' => $result->getVisits(),
				'Visitantes' => $result->getVisitors(),
				'Páginas Vistas' => $result->getPageviews(),
			);
		}

		$data[$profile_id] = array(
			'name' => $_SESSION['accounts'][$profile_id],
			'Visitas' => $ga->getVisits(),
			'Visitantes' => $ga->getVisitors(),
			'Páginas vistas' => $ga->getPageviews(),
			'countries' => $countries
		);
		//debug($data);
	}


}

require('PDF/fpdf.php');

class PDF extends FPDF {

	function BasicTable($header, $data) {
		// Cabecera
		$this->SetFont('','B');
		$i = 0;
		foreach($header as $col) {
			if ($i == 0) {
				$width = 80;
			} else {
				$width = 35;
			}
			$i ++;
			$this->Cell($width,7,utf8_decode($col),1);
		}
		$this->Ln();
		// Datos
		$this->SetFont('Arial','',14);
		foreach($data as $row) {
			$i = 0;
			foreach($row as $col) {
				if ($i == 0) {
					$this->Cell(80, 6, $col, 1);
				} else {
					$this->Cell(35, 6, number_format($col, 0, ',', '.'), 1, 0, 'R');
				}
				$i ++;
			}
			$this->Ln();
		}
	}

}

$pdf = new PDF();

$header = array('Web', 'Visitas', 'Visitantes', 'P. Vistas');

$pdf->SetFont('Arial','',14);
if ($queryType == 4) {
	foreach  ($data as $key => $value) {
		$pdf->AddPage();
		$pdf->BasicTable($header, $value['countries']);
	}
} else {
	$pdf->AddPage();
	$pdf->BasicTable($header, $data);
}
$pdf->Output();
