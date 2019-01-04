<?php

// include "include/frontend/templates/header.php";
// include "include/frontend/templates/menu.php";
// include "include/frontend/templates/footer.php";

include_once "include/include_all.php";

$menu_items = array(
  "Status" => "index.php",
  "Temperaturas" => "temp_hist.php",
  "Eventos" => "events.php"
);

$pb = new PageBuilder;
$pb->build_menu("OOP",$menu_items);
$pb->append_body("opaaaaa");
$pb->append_body("Tudo bem ae");

$pb->render_full_html();

 ?>
