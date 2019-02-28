<?php

include "db/file_db_system.php";

// echo "<pre>";

$fDB = new FileToDb;
$fDB->readFiles("files");
// var_dump($fDB->readContents("temp"));

$html = "
<h2>Section title</h2>
<div class=\"table-responsive\">
  <table class=\"table table-striped\">
    <thead>
      <tr>
        <th>#</th>
        <th>time</th>
        <th>millis</th>
        <th>tempc0</th>
        <th>millis</th>
        <th>tempc0</th>
        <th>tempc1</th>
        <th>heat_pump</th>
        <th>filename</th>
      </tr>
    </thead>
    <tbody>
";

foreach ($fDB->readContents("temp") as $key => $value) {
  $html .= "
  <tr>
  <td>".$key."</td>
  <td>$value->time</td>
  <td>$value->millis</td>
  <td>$value->tempc0</td>
  <td>$value->millis</td>
  <td>$value->tempc0</td>
  <td>$value->tempc1</td>
  <td>$value->heat_pump</td>
  <td>$value->filename</td>
  </tr>
  ";

}

$html .= "              </tbody>
            </table>
          </div>";

echo $html;
 ?>
