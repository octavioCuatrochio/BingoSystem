<?php

require_once('tcpdf/tcpdf.php');
require_once "Model/Model.php";

$html = '
 <style>
table {
    font-family: arial, sans-serif;
    border-collapse: collapse;
    width: 100%;
}

td, th {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 8px;
}

tr:nth-child(even) {
    background-color: #dddddd;
}
</style>
<table>

';

$model = new Model();
$cards = $model->GetBingoCards();

foreach ($cards as $card) {

	$i = 0;
	$matrix = [];
	$table_id = $card->id;

	$array = explode("//", $card->numeros);
	array_pop($array);

	foreach ($array as $element) {
		$matrix[$i] =  explode("/", $element);
		$i++;
	}

	foreach ($matrix as $row) {

		$html .= '<tr>';

		foreach ($row as $number) {
			if ($number == "null") {
				$html .= '<td>' . "   " . '</td>';
			} else {
				$html .= '<td>' . $number . '</td>';
			}
		}

		$html .= '</tr>';
	}
	$html .= '<br>';
}
$html .= '</table>';



$obj_pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$obj_pdf->SetCreator(PDF_CREATOR);
$obj_pdf->SetTitle("Export HTML Table data to PDF using TCPDF in PHP");
$obj_pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);
$obj_pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$obj_pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$obj_pdf->SetDefaultMonospacedFont('helvetica');
$obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$obj_pdf->SetMargins(PDF_MARGIN_LEFT, '5', PDF_MARGIN_RIGHT);
$obj_pdf->setPrintHeader(false);
$obj_pdf->setPrintFooter(false);
$obj_pdf->SetAutoPageBreak(TRUE, 10);
$obj_pdf->SetFont('helvetica', '', 12);
$obj_pdf->AddPage();
$obj_pdf->writeHTML($html);
$obj_pdf->Output('sample.pdf', 'I');
