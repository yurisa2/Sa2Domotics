<?php
include_once "include/include_all.php";

function convert_pump_time($times) {

  $days = array();
  $hours = array();

foreach ($times as $key => $value) {

  if(substr($value,0,2) == 'w0') $days[] = "Domingo";
  if(substr($value,0,2) == 'w1') $days[] = "Segunda";
  if(substr($value,0,2) == 'w2') $days[] = "Terça";
  if(substr($value,0,2) == 'w3') $days[] = "Quarta";
  if(substr($value,0,2) == 'w4') $days[] = "Quinta";
  if(substr($value,0,2) == 'w5') $days[] = "Sexta";
  if(substr($value,0,2) == 'w6') $days[] = "Sábado";
  if(substr($value,0,1) != 'w') $hours[] = $value;
}

return array($days,$hours);
}

$times_file = file_get_contents("include/main_pump_config.json.txt");
$times_file = json_decode($times_file);

// var_dump($times_file);
// print_r(convert_pump_time($times_file)[0]);
// print_r(convert_pump_time($times_file)[1]);
// exit;
$html = "<br><br><br>
<h2>Bomba principal</h2>
Agora: ".date("w - G:i:s",time())."<br>
<h5>Configurado:</h5>
Dias:
";
foreach(convert_pump_time($times_file)[0] as $key => $value) {
  $html .= " ".$value.", ";
}
$html .= "<br> Horas:";
foreach(convert_pump_time($times_file)[1] as $key => $value) {
  $html .= " ".$value.", ";
}

$html .= "<br><br><br><br>";
$html .= "

<div class=\"container\">
    <div class=\"row\">
    <form id=\"form1\" name=\"form1\" method=\"post\" action=\"main_pump_time_picker_be.php\" method=\"post\">

                <div class=\"col-sm-12\">
                <h2>Nova Configuração</h2>
                <h4>Horas</h4>
                <div class=\"row\">
    <label style=\"  padding: 5px;\" for=\"00\"> 00 </label>
    <input type=\"checkbox\" name=\"00\" value=\"00\" id=\"00\" /><br class=\"clear\" />

    <label style=\"  padding: 5px;\" for=\"01\"> 01 </label>
    <input type=\"checkbox\" name=\"01\" value=\"01\" id=\"01\" /><br class=\"clear\" />

    <label style=\"  padding: 5px;\" for=\"02\"> 02 </label>
    <input type=\"checkbox\" name=\"02\" value=\"02\" id=\"02\" /><br class=\"clear\" />

    <label style=\"  padding: 5px;\" for=\"03\"> 03 </label>
    <input type=\"checkbox\" name=\"03\" value=\"03\" id=\"03\" /><br class=\"clear\" />

    <label style=\"  padding: 5px;\" for=\"04\"> 04 </label>
    <input type=\"checkbox\" name=\"04\" value=\"04\" id=\"04\" /><br class=\"clear\" />

    <label style=\"  padding: 5px;\" for=\"05\"> 05 </label>
    <input type=\"checkbox\" name=\"05\" value=\"05\" id=\"05\" /><br class=\"clear\" />

    <label style=\"  padding: 5px;\" for=\"06\"> 06 </label>
    <input type=\"checkbox\" name=\"06\" value=\"06\" id=\"06\" /><br class=\"clear\" />

    <label style=\"  padding: 5px;\" for=\"07\"> 07 </label>
    <input type=\"checkbox\" name=\"07\" value=\"07\" id=\"07\" /><br class=\"clear\" />

    <label style=\"  padding: 5px;\" for=\"08\"> 08 </label>
    <input type=\"checkbox\" name=\"08\" value=\"08\" id=\"08\" /><br class=\"clear\" />

    <label style=\"  padding: 5px;\" for=\"09\"> 09 </label>
    <input type=\"checkbox\" name=\"09\" value=\"09\" id=\"09\" /><br class=\"clear\" />

    <label style=\"  padding: 5px;\" for=\"10\"> 10 </label>
    <input type=\"checkbox\" name=\"10\" value=\"10\" id=\"10\" /><br class=\"clear\" />

    <label style=\"  padding: 5px;\" for=\"11\"> 11 </label>
    <input type=\"checkbox\" name=\"11\" value=\"11\" id=\"11\" /><br class=\"clear\" />

        </div>
        </div>

         <div class=\"col-sm-12\" >
            <div class=\"row\">

            <label style=\"  padding: 5px;\" for=\"12\"> 12 </label>
            <input type=\"checkbox\" name=\"12\" value=\"12\" id=\"12\" /><br class=\"clear\" />

        <label style=\"  padding: 5px;\" for=\"13\"> 13 </label>
        <input type=\"checkbox\" name=\"13\" value=\"13\" id=\"13\" /><br class=\"clear\" />

        <label style=\"  padding: 5px;\" for=\"14\"> 14 </label>
        <input type=\"checkbox\" name=\"14\" value=\"14\" id=\"14\" /><br class=\"clear\" />

        <label style=\"  padding: 5px;\" for=\"15\"> 15 </label>
        <input type=\"checkbox\" name=\"15\" value=\"15\" id=\"15\" /><br class=\"clear\" />

        <label style=\"  padding: 5px;\" for=\"16\"> 16 </label>
        <input type=\"checkbox\" name=\"16\" value=\"16\" id=\"16\" /><br class=\"clear\" />

        <label style=\"  padding: 5px;\" for=\"17\"> 17 </label>
        <input type=\"checkbox\" name=\"17\" value=\"17\" id=\"17\" /><br class=\"clear\" />

        <label style=\"  padding: 5px;\" for=\"18\"> 18 </label>
        <input type=\"checkbox\" name=\"18\" value=\"18\" id=\"18\" /><br class=\"clear\" />

        <label style=\"  padding: 5px;\" for=\"19\"> 19 </label>
        <input type=\"checkbox\" name=\"19\" value=\"19\" id=\"19\" /><br class=\"clear\" />

        <label style=\"  padding: 5px;\" for=\"20\"> 20 </label>
        <input type=\"checkbox\" name=\"20\" value=\"20\" id=\"20\" /><br class=\"clear\" />

        <label style=\"  padding: 5px;\" for=\"21\"> 21 </label>
        <input type=\"checkbox\" name=\"21\" value=\"21\" id=\"21\" /><br class=\"clear\" />

        <label style=\"  padding: 5px;\" for=\"22\"> 22 </label>
        <input type=\"checkbox\" name=\"22\" value=\"22\" id=\"22\" /><br class=\"clear\" />

        <label style=\"  padding: 5px;\" for=\"23\"> 23 </label>
        <input type=\"checkbox\" name=\"23\" value=\"23\" id=\"23\" /><br class=\"clear\" />
            </div>
            </div>
         <div class=\"col-sm-12\" >
         <h4>Dias</h4>
            <div class=\"row\">

            <label style=\"  padding: 5px;\" for=\"w0\">Domingo</label>
            <input type=\"checkbox\" name=\"w0\" value=\"w0\" id=\"w0\" /><br class=\"clear\" />

            <label style=\"  padding: 5px;\" for=\"w1\">Segunda</label>
            <input type=\"checkbox\" name=\"w1\" value=\"w1\" id=\"w1\" /><br class=\"clear\" />

            <label style=\"  padding: 5px;\" for=\"w2\">Terça</label>
            <input type=\"checkbox\" name=\"w2\" value=\"w2\" id=\"w2\" /><br class=\"clear\" />

            <label style=\"  padding: 5px;\" for=\"w3\">Quarta</label>
            <input type=\"checkbox\" name=\"w3\" value=\"w3\" id=\"w3\" /><br class=\"clear\" />

            <label style=\"  padding: 5px;\" for=\"w4\">Quinta</label>
            <input type=\"checkbox\" name=\"w4\" value=\"w4\" id=\"w4\" /><br class=\"clear\" />

            <label style=\"  padding: 5px;\" for=\"w5\">Sexta</label>
            <input type=\"checkbox\" name=\"w5\" value=\"w5\" id=\"w5\" /><br class=\"clear\" />

            <label style=\"  padding: 5px;\" for=\"w6\">Sábado</label>
            <input type=\"checkbox\" name=\"w6\" value=\"w6\" id=\"w6\" /><br class=\"clear\" />

            </div>
            </div>
</div>

  <input type=\"submit\" value=\"Salvar\">
  </form>
</div>



";


                    $pb = new PageBuilder;
                    $pb->build_menu($menu_title,$menu_items);
                    $pb->append_body($html);

                    $pb->render_full_html();


?>
