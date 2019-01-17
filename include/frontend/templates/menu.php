<?php
function menu_output($menu_title,$menu_items) {


$template ="
<nav class=\"navbar navbar-toggleable-md navbar-inverse fixed-top bg-inverse\">
  <button class=\"navbar-toggler navbar-toggler-right hidden-lg-up\" type=\"button\" data-toggle=\"collapse\" data-target=\"#navbarsExampleDefault\"
aria-controls=\"navbarsExampleDefault\" aria-expanded=\"false\" aria-label=\"Toggle navigation\">
    <span class=\"navbar-toggler-icon\"></span>
  </button>
  <a class=\"navbar-brand\" href=\"".DEFAULT_INDEX."\">$menu_title</a>

  <div class=\"collapse navbar-collapse\" id=\"navbarsExampleDefault\">
    <ul class=\"navbar-nav mr-auto\">";

foreach ($menu_items as $key => $value) {
  // code...
    $template .= "

      <li class=\"nav-item active\">
        <a class=\"nav-link\" href=\"$value\">$key</a>
      </li>
";
}

$template .= "

    </ul>
    </div>
</nav>
";

return $template;
};
?>
