<?php
include_once "include/include_all.php";


$times_main_pump = json_encode($_POST);

file_put_contents("include/main_pump_config.json.txt",$times_main_pump);



$html = "<br><br><br><br><br><br>
<h2>Config Salva</h2>




";


                    $pb = new PageBuilder;
                    $pb->build_menu($menu_title,$menu_items);
                    $pb->append_body($html);

                    $pb->render_full_html();

?>
