<?php

$method = $_GET["method"];

$tempc0 = $_GET["tempc0"];
$tempc1 = $_GET["tempc1"];
$millis = $_GET["millis"];
$heat_pump = $_GET["heat_pump"];

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

echo 0;
}

if($method == "permission_diag") {

$hora = date("H",time());
if( $hora > 19 || $hora < 7) $perm = 0;
else $perm = 1;

echo $perm;

$content = time() . " perm = ". $perm;

file_put_contents("watch_perm.json",$content);
}
