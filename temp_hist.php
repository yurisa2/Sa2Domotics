<?php
include "db/file_db_system.php";

$json_temp = file_get_contents("temp.json");
$json_temp = json_decode($json_temp);
$time = $json_temp->time;

$dia_hora = date('d/m/y G:i',$time);


$fDB = new FileToDb;
$fDB->readFiles("files");
// var_dump($fDB->readContents("temp"));

$html = "
<h2>Histórico Temperatura</h2>
<div class=\"table-responsive\">
  <table class=\"table table-striped\">
    <thead>
      <tr>
        <th>Hora</th>
        <th>Piscina</th>
        <th>Aquecedor</th>
        <th>Bomba</th>
      </tr>
    </thead>
    <tbody>
";

foreach ($fDB->readContents("temp") as $key => $value) {
  $html .= "
  <tr>
  <td>".date('Y-m-d G:i:s',$value->time)."</td>
  <td>$value->tempc0</td>
  <td>$value->tempc1</td>
  <td>$value->heat_pump</td>
  </tr>
  ";

}

$html .= "              </tbody>
            </table>
          </div>";

echo '<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>Sa2 Domotics control</title>
    <link rel="canonical" href="https://getbootstrap.com/docs/4.0/examples/dashboard/">

    <!-- Bootstrap core CSS -->
    <link href="https://v4-alpha.getbootstrap.com/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="dashboard.css" rel="stylesheet">
  </head>

  <body>
    <nav class="navbar navbar-toggleable-md navbar-inverse fixed-top bg-inverse">
      <button class="navbar-toggler navbar-toggler-right hidden-lg-up" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault"
aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <a class="navbar-brand" href="#">Status Casa</a>

      <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item active">
           <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="temp_hist.php">Temperaturas</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="events.php">Eventos</a>
          </li>
        </ul>
      </div>
    </nav>

        </nav>

        <main class="col-sm-9 offset-sm-3 col-md-10 offset-md-2 pt-3">
        <!-- <main> -->
          <h1>Dashboard</h1>
          Ultimo contato: '.$dia_hora.'
<br>

          '.$html.'
        </main>
      </div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n"
crossorigin="anonymous"></script>
    <script>window.jQuery || document.write(\'<script src="../../assets/js/vendor/jquery.min.js"><\/script>\')</script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js"
integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
    <script src="https://v4-alpha.getbootstrap.com/dist/js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="https://v4-alpha.getbootstrap.com/assets/js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>';
?>
