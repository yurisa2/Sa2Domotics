<?php
error_reporting(E_ERROR | E_PARSE);

$method = $_GET["method"];

$tempc0 = $_GET["tempc0"];
$tempc1 = $_GET["tempc1"];
$millis = $_GET["millis"];
$heat_pump = $_GET["heat_pump"];
$heat_pump = $_GET["main_pump"];

$event_text = $_GET["event_text"];

if($method == "temp_report") {

$content = array();
$content["time"] = time();
$content["millis"] = $millis;
$content["tempc0"] = $tempc0;
$content["tempc1"] = $tempc1;
$content["heat_pump"] = $heat_pump;
$content["main_pump"] = $main_pump;

file_put_contents("temp.json",json_encode($content));
file_put_contents("files/temp_".time().".json",json_encode($content));

echo "OK Temp received!";
}

if($method == "events") {

$content = array();
$content["time"] = time();
$content["millis"] = $millis;
$content["event_text"] = $event_text;

file_put_contents("files/event_".time().".json",json_encode($content));

echo "OK Event received!";
}

if($method == "main_pump") {

  $config_main_pump = file_get_contents("include/main_pump_config.json.txt");
  $config_main_pump = json_decode($config_main_pump);


  $now_weekday = "w";
  $now_weekday .= date('w',time());
  $now_hour = date('h',time());

  $result_sum;
  foreach ($config_main_pump as $key => $value) {
     if($now_weekday == $value ) $result_sum++;
     if($now_hour == $value ) $result_sum++;
  }

  if($result_sum == 2) echo 1;
  else echo 0;
}

if($method == "permission_diag") {

$hora = date("H",time());
if( $hora > 19 || $hora < 7) $perm = 0;
else $perm = 1;

echo $perm;
}


if($method == "test_mode") {

}


?>
