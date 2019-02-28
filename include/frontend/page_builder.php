<?php

include "include/frontend/templates/header.php";
include "include/frontend/templates/menu.php";
include "include/frontend/templates/footer.php";

class PageBuilder {

  function __construct() {
    $this->header = header_output();
    $this->footer = footer_output();
  }

  function build_menu($menu_title,$menu_items) {
    $this->menu = menu_output($menu_title,$menu_items);
  }

  function append_body($body) {
    if(!empty($this->body))$this->body .= $body;
    else $this->body = $body;

  }

  function render_full_html() {

    $html = $this->header;
    $html .= $this->menu;
    $html .= $this->body;
    $html .= $this->footer;

    echo $html;
  }

};

 ?>
