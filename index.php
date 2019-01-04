<?php

include_once "include/include_all.php";

$json_temp = file_get_contents("temp.json");
$json_temp = json_decode($json_temp);

$time = $json_temp->time;
$millis = $json_temp->millis;
$tempc0 = $json_temp->tempc0;
$tempc1 = $json_temp->tempc1;
$heat_pump = $json_temp->heat_pump;

$temp_min = min($tempc0,$tempc1);
$temp_max = max($tempc0,$tempc1);

$temp_delta = $tempc1 - $tempc0;

$temp_p_min = $temp_min - abs($temp_delta);
$temp_p_max = $temp_max;

$mult_factor = 100/($temp_p_max-$temp_p_min);

$temppc0 = ($tempc0 - $temp_p_min) * $mult_factor;
$temppc1 = ($tempc1 - $temp_p_min) * $mult_factor;

//  var_dump($temp_p_min);
// exit;

if($heat_pump) {
  $heat_pump = "LIGADA";
  $animada = "progress-bar-striped progress-bar-animated";
} else {
  $heat_pump = "DESLIGADA";
  $animada = "";
}


$dia_hora = date('Y-m-d h:i:s',$time);


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
          <div class="progress"  style="height: 50px ;font-size:20px;">
            <div class="progress-bar '.$animada.'" role="progressbar" aria-valuenow="'.$temppc0.'" aria-valuemin="'.$temp_p_min.'"
aria-valuemax="'.$temp_p_max.'"
style="width: '.$temppc0.'%; height: 100%;">
              '.$tempc0.'
            </div>
          </div>
Aquecedor:
<div class="progress"  style="height: 50px ;font-size:20px;">
            <div class="progress-bar '.$animada.' bg-danger" role="progressbar" aria-valuenow="'.$temppc1.'" aria-valuemin="'.$temp_p_min.'"
aria-valuemax="'.$temp_p_max.'" style="width: '.$temppc1.'%; height: 100%;">
		'.$tempc1.'
</div>
          </div>


          <br>

          <div class="card" style="width: 18rem;">
            <div class="card-body">
              <h5 class="card-title">Status sistema:</h5>
              <p class="card-text">Bomba de aquecimento:
              <div class="progress"  style="height: 20px ;font-size:20px;">
                          <div class="progress-bar '.$animada.' bg-warning" role="progressbar" aria-valuenow="100"
              aria-valuemin="0"
              aria-valuemax="100" style="width: 100%; height: 100%;">
              		'.$heat_pump.'
              </div>
              </div>

</p>

              <p class="card-text">Tempo ligado:'.secondsToTime(round($millis / 1000)).'</p>


              <!-- <a href="#" class="btn btn-primary">Go somewhere</a> -->
            </div>
          </div>';


          $pb = new PageBuilder;
          $pb->build_menu($menu_title,$menu_items);
          $pb->append_body($body);

          $pb->render_full_html();

?>
