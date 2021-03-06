<?php
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);

include_once "include/include_all.php";
unlink("temp_plot.png");

$json_temp = file_get_contents("temp.json");
$json_temp = json_decode($json_temp);
$time = $json_temp->time;

$dia_hora = date('d/m/y G:i',$time);

$db = new sa2_db;

var_dump($db);

$html_pre = "<br><br><br>
<h2>Histórico Temperatura</h2>
<div class=\"table-responsive\">
  <table class=\"table table-striped\">
    <thead>
      <tr>
        <th>Hora</th>
        <th>Piscina</th>
        <th>Aquecedor</th>
        <th>Delta</th>
        <th>B.Aquecedor</th>
        <th>B.Principal</th>
      </tr>
    </thead>
    <tbody>
";

$plot_data = array();
$plot_delta = array();
$temps = array();
$time_24h = time() - 86400;

$html = "";

foreach ($db->select_temps(100000) as $key => $value) {
  if($value["time"] > $time_24h) {

    $delta_local = $value["tempc1"]-$value["tempc0"];

  $plot_data[] = array(
    '',
    $value["time"],
    $value["tempc0"],
    $value["tempc1"]
  );

  $plot_delta[] = array(
    '',
    $value["time"],
    $delta_local);


  $temps[] = $value["tempc1"];

}

if(empty($value["main_pump"]) || $value["main_pump"] = "" ) $value_main_pump = 0;
else {
$value_main_pump = 1;

}

  $html .= "
  <tr>
  <td>".date('Y-m-d G:i:s',$value["time"])."</td>
  <td>".round($value["tempc0"],2)."</td>
  <td>".round($value["tempc1"],2)."</td>
  <td>".round($delta_local,2)."</td>
  <td>$value[heat_pump]</td>
  <td>$value_main_pump</td>
  </tr>
  ";
}

$html .= "              </tbody>
            </table>
          </div>";


                    $plot = new PHPlot(600, 400);
                    $plot->SetImageBorderType('plain');

                    $plot->SetIsInline(true);
                    $plot->SetTitle('Temperatura ultimas 24 h');
                    $plot->SetLegend(array('Piscina', 'Aquecedor'));
                    $plot->SetDataColors(array('blue','red'));
                    $plot->SetPlotType('lines');
                    $plot->SetDataType('data-data');
                    $plot->SetDataValues($plot_data);
                    $plot->SetXLabelType('time');
                    $plot->SetXTimeFormat('%H:%M');
                    $plot->SetPlotAreaWorld(NULL, min($temps), NULL, max($temps));
                    $plot->SetOutputFile("temp_plot.png");

                    $plot->DrawGraph();
                    // exit;

                    $plot_delta_graph = new PHPlot(600, 400);
                    $plot_delta_graph->SetImageBorderType('plain');

                    $plot_delta_graph->SetIsInline(true);
                    $plot_delta_graph->SetTitle('Delta ultimas 24 h');
                    $plot_delta_graph->SetLegend(array('Delta'));
                    $plot_delta_graph->SetDataColors(array('green'));
                    $plot_delta_graph->SetPlotType('lines');
                    $plot_delta_graph->SetDataType('data-data');
                    $plot_delta_graph->SetDataValues($plot_delta);
                    $plot_delta_graph->SetXLabelType('time');
                    $plot_delta_graph->SetXTimeFormat('%H:%M');
                    // $plot_delta_graph->SetPlotAreaWorld(NULL, min($plot_delta), NULL, max($plot_delta));
                    $plot_delta_graph->SetOutputFile("temp_plot_delta.png");

                    $plot_delta_graph->DrawGraph();
                    // exit;

                    $html_plot = '<img src="temp_plot.png" align="center" class="img-fluid">';
                    $html_plot .= '<img src="temp_plot_delta.png" align="center" class="img-fluid">';


                    $html_total = $html_pre.$html_plot.$html;


                    $pb = new PageBuilder;
                    $pb->build_menu(MENU_TITLE,$menu_items);
                    $pb->append_body($html_total);

                    $pb->render_full_html();


?>
