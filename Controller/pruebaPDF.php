<?php

require_once '../dompdf/autoload.inc.php';
require_once "../libs/smarty/Smarty.class.php";
require_once "../Model/Model.php";

use Dompdf\Dompdf;

// $smarty = new Smarty();
// $html = $smarty->fetch('../templates/template.tpl');
$html = '../prueba.html';
$filename = '../pdf/file.pdf';

$dompdf = new Dompdf();


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

        $html .= '
    <div>' . '<h1>' . $table_id . '</h1>'. '
  <tr>
   <td>' . $row[0] . '</td>
   <td>' . $row[1] . '</td>
   <td>' . $row[2] . '</td>
   <td>' . $row[3] . '</td>
   <td>' . $row[4] . '</td>
   <td>' . $row[5] . '</td>
   <td>' . $row[6] . '</td>
   <td>' . $row[7] . '</td>
   <td>' . $row[8] . '</td>
  </tr>
  </div>
 ';
    }

    $html .= '<br>';
}
$html .= '</table>';

echo ($html);
die();





$dompdf->set_paper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("PruebaTabla", array("Attachment" => 0));
// file_put_contents($filename, $dompdf->output());

die();
