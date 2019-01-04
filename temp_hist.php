<?php
include_once "include/include_all.php";


$json_temp = file_get_contents("temp.json");
$json_temp = json_decode($json_temp);
$time = $json_temp->time;

$dia_hora = date('d/m/y G:i',$time);


$fDB = new FileToDb;
$fDB->readFiles("files");
// var_dump($fDB->readContents("temp"));

$html = "<br><br><br>
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

                    $pb = new PageBuilder;
                    $pb->build_menu($menu_title,$menu_items);
                    $pb->append_body($html);

                    $pb->render_full_html();

?>
