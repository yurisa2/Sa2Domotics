<?php

include_once "include/include_all.php";


$inserto = [
  "time" => 12341234,
  "millis" => 987654,
  "tempc0" => 99.9,
  "tempc1" => 11.1,
  "heat_pump" => 0,
  "main_pump" => 1
];

$db = new sa2_db;

$fDB = new FileToDb;
$fDB->readFiles("files");


echo "<pre>";
foreach ($fDB->readContents("temp") as $key => $value) {
  // $db->insert_temp_logger($inserto);
  var_dump($value);
  exit;

}



?>
