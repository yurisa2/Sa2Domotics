<?php

include_once "include/include_all.php";

$menu_items = array(
  "Status" => "index.php",
  "Temperaturas" => "temp_hist.php",
  "Eventos" => "events.php"
);

$json_temp = file_get_contents("temp.json");

$json_temp = json_decode($json_temp);

//var_dump($json_temp);

$time = $json_temp->time;
$millis = $json_temp->millis;
$tempc0 = $json_temp->tempc0;
$tempc1 = $json_temp->tempc1;
$heat_pump = $json_temp->heat_pump;

$temppc0 = ($tempc0 - 10) * 3.33;
$temppc1 = ($tempc1 - 10) * 3.33;



if($heat_pump) {
$heat_pump = "LIGADA";
$animada = "progress-bar-striped progress-bar-animated";
} else {
$heat_pump = "DESLIGADA";
$animada = "";

}


$dia_hora = date('d/m/y G:i',$time);


//var_dump($temppc0);
//var_dump($temppc1);

function secondsToTime($seconds) {
    $dtF = new \DateTime('@0');
    $dtT = new \DateTime("@$seconds");
    return $dtF->diff($dtT)->format('%a d, %h:%i:%s');
}

$body = '<main class="col-sm-9 offset-sm-3 col-md-10 offset-md-2 pt-3">
        <!-- <main> -->
          <h1>Dashboard</h1>
          Ultimo contato: '.$dia_hora.'
<br>
<br>
          Piscina:
          <div class="progress"  style="height: 50px ;font-size:20px;">
            <div class="progress-bar '.$animada.'" role="progressbar" aria-valuenow="'.$temppc0.'" aria-valuemin="10"
aria-valuemax="40"
style="width: '.$temppc0.'%; height: 100%;">
              '.$tempc0.'
            </div>
          </div>
<br>
Aquecedor:
<div class="progress"  style="height: 50px ;font-size:20px;">
            <div class="progress-bar '.$animada.' bg-danger" role="progressbar" aria-valuenow="'.$temppc1.'"
aria-valuemin="10"
aria-valuemax="40" style="width: '.$temppc1.'%; height: 100%;">
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
          $pb->build_menu("Teste New Index",$menu_items);
          $pb->append_body($body);

          $pb->render_full_html();

?>
