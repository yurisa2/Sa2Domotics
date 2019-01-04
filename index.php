<?php

include_once "include/include_all.php";

$json_temp = file_get_contents("temp.json");
$json_temp = json_decode($json_temp);

$main_pump = 0;
$vazao = 4;

$time = $json_temp->time;
$millis = $json_temp->millis;
$tempc0 = $json_temp->tempc0;
$tempc1 = $json_temp->tempc1;
$heat_pump = $json_temp->heat_pump;
$heat_pump = $json_temp->main_pump;

$fDB = new FileToDb;
$fDB->readFiles("files");

$time_24h = time() - 86400;
$time_1wk = time() - (86400 * 7) ;
$time_1mo = time() - (86400 * 30) ;


foreach ($fDB->readContents("temp") as $key => $value) {
  if($value->time > $time_24h) {
    $temps_24h[] = $value->tempc1;
    $deltas_24h[] =  $value->tempc1-$value->tempc0;
  };
  if($value->time > $time_1wk) {
    $temps_1wk[] = $value->tempc1;
    $deltas_1wk[] =  $value->tempc1-$value->tempc0;
  };
  if($value->time > $time_1mo) {
    $temps_1mo[] = $value->tempc1;
    $deltas_1mo[] =  $value->tempc1-$value->tempc0;
  };

  if($value->time > $time_24h && $value->heat_pump == 1) {
    $tempc1_24h[] = $value->tempc1;
    $tempc0_24h[] = $value->tempc0;
    $delta_local_24h[] = $value->tempc1-$value->tempc0;
  };
  if($value->time > $time_1wk && $value->heat_pump == 1) {
    $tempc1_1wk[] = $value->tempc1;
    $tempc0_1wk[] = $value->tempc0;
    $delta_local_1wk[] = $value->tempc1-$value->tempc0;
  };
  if($value->time > $time_1mo && $value->heat_pump == 1) {
    $tempc1_1mo[] = $value->tempc1;
    $tempc0_1mo[] = $value->tempc0;
    $delta_local_1mo[] = $value->tempc1-$value->tempc0;
  };
}

function avg($a) {
  // $a = array_filter($a);
  $average = array_sum($a)/count($a);
  return $average;
}
$temp_delta = $tempc1 - $tempc0;

$watts = round(($temp_delta*60*$vazao)/853*1000);
if($watts <= 0) $watts = 0;

$watts_24h = round((array_sum($delta_local_24h)*$vazao)/853*1000);
$watts_1wk= round((array_sum($delta_local_1wk)*$vazao)/853*1000);
$watts_1mo= round((array_sum($delta_local_1mo)*$vazao)/853*1000);

$watts_m = $watts / 65;


$temp_min = min($temps_24h);
$temp_max = max($temps_24h);


$temp_p_min = $temp_min - abs($temp_delta);
$temp_p_max = $temp_max;

$mult_factor = 100/($temp_p_max-$temp_p_min);

$temppc0 = ($tempc0 - $temp_p_min) * $mult_factor;
$temppc1 = ($tempc1 - $temp_p_min) * $mult_factor;

$min_delta_calc = min($deltas_24h);
$max_delta_calc = max($deltas_24h);

$temp_delta_ppc = ($temp_delta+abs($min_delta_calc))* (100/(abs($min_delta_calc)+abs($max_delta_calc)));

if($heat_pump) {
  $heat_pump = "LIGADA";
  $animada = "progress-bar-striped progress-bar-animated";
} else {
  $heat_pump = "DESLIGADA";
  $animada = "";
}

if($main_pump) {
  $main_pump = "LIGADA";
  $animada_main = "progress-bar-striped progress-bar-animated";
} else {
  $main_pump = "DESLIGADA";
  $animada_main = "";
}


$dia_hora = date('Y-m-d G:i:s',$time);


//var_dump($temppc0);
//var_dump($temppc1);

function secondsToTime($seconds) {
  $dtF = new \DateTime('@0');
  $dtT = new \DateTime("@$seconds");
  return $dtF->diff($dtT)->format('%a d, %h:%i:%s');
}

$body = '
<br><br>

<h1>Dashboard</h1>
Ultimo contato: '.$dia_hora.'
<br>
<br>
Piscina:
<div class="progress"  style="height: 40px ;font-size:20px;">
<div class="progress-bar '.$animada.'" role="progressbar" aria-valuenow="'.$temppc0.'" aria-valuemin="'.$temp_p_min.'"
aria-valuemax="'.$temp_p_max.'"
style="width: '.$temppc0.'%; height: 100%;">
'.$tempc0.'
</div>
</div>
Aquecedor:
<div class="progress"  style="height: 40px ;font-size:20px;">
<div class="progress-bar '.$animada.' bg-danger" role="progressbar" aria-valuenow="'.$temppc1.'" aria-valuemin="'.$temp_p_min.'"
aria-valuemax="'.$temp_p_max.'" style="width: '.$temppc1.'%; height: 100%;">
'.$tempc1.'
</div>
</div>
Delta:
<div class="progress"  style="height: 20px ;font-size:20px;">
<div class="progress-bar '.$animada.' bg-success" role="progressbar" aria-valuenow="'.$temp_delta.'" aria-valuemin="-1"
aria-valuemax="7" style="width: '.$temp_delta_ppc.'%; height: 100%;">
'.$temp_delta.'
</div>
</div>


<br>

<div class="card" style="width: 18rem;">
<div class="card-body">
<h5 class="card-title">Status sistema:</h5>

<p class="card-text">Bomba de aquecimento:
<div class="progress"  style="height: 20px ;font-size:20px;">
<div class="progress-bar '.$animada.' bg-warning" role="progressbar" aria-valuenow="100"
aria-valuemin="0" aria-valuemax="100" style="width: 100%; height: 100%;">'.$heat_pump.'
</div>
</div>
</p>
<p class="card-text">Bomba Principal:
<div class="progress"  style="height: 20px ;font-size:20px;">
<div class="progress-bar '.$animada_main.' bg-warning" role="progressbar" aria-valuenow="100"
aria-valuemin="0" aria-valuemax="100" style="width: 100%; height: 100%;">'.$heat_pump.'
</div>
</div>
</p>

<p class="card-text">Tempo ligado:'.secondsToTime(round($millis / 1000)).'</p>
</div>
</div>

';




$body .= '<div class="card" style="width: 18rem;">
<div class="card-body">
<h5 class="card-title">Energia instantanea:</h5>
<p class="card-text">Watts: '.$watts.'
<br>Watts/m: '.round($watts_m,2).'</p>
<p class="card-text">Assumindo: vazão de 4L/m, 65m de exposicao</p>
<h5 class="card-title">Energia acumulada:</h5>
<p class="card-text">24h: '.$watts_24h.' Watts
<br>1 semana: '.$watts_1wk.' Watts
<br>1 Mês: '.$watts_1mo.' Watts</p>
</div>
</div>';

$body .= '<div class="card" style="width: 18rem;">
<div class="card-body">
<h5 class="card-title">Estatisticas:</h5>
<p class="card-text">Funcionamento
<br>24h: '.round(count($delta_local_24h)/60,2).'h
<br>Semana : '.round(count($delta_local_1wk)/60,2).'h
<br>Mês : '.round(count($delta_local_1mo)/60,2).'h</p>

<p class="card-text">Temperatura (Max | Min)
<br>24h: '.max($temps_24h).' | '.min($temps_24h).'
<br>Semana: '.max($temps_1wk).' | '.min($temps_1wk).'
<br>Mês: '.max($temps_1mo).' | '.min($temps_1mo).'</p>

<p class="card-text">Deltas (Max | Min)
<br>24h: '.max($deltas_24h).' | '.min($deltas_24h).'
<br>Semana: '.max($deltas_1wk).' | '.min($deltas_1wk).'
<br>Mês: '.max($deltas_1mo).' | '.min($deltas_1mo).'</p>

</div>
</div>';

$pb = new PageBuilder;
$pb->build_menu($menu_title,$menu_items);
$pb->append_body($body);

$pb->render_full_html();

?>
