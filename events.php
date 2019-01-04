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
<h2>Histórico Eventos</h2>
<div class=\"table-responsive\">
  <table class=\"table table-striped\">
    <thead>
      <tr>
        <th>Hora</th>
        <th>Texto do Evento</th>
      </tr>
    </thead>
    <tbody>
";

foreach ($fDB->readContents("event") as $key => $value) {
  $html .= "
  <tr>
  <td>".gmdate('Y-m-d G:i:s',$value->time)."</td>
  <td>$value->event_text</td>
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
