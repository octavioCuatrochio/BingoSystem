<?php

require_once "../Model/Model.php";


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
    <div>
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
}
$html .= '</table>';


