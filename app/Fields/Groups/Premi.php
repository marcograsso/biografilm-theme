<?php

namespace App\Fields\Groups;

use Extended\ACF\Location;

$prizes_fields = require get_stylesheet_directory() . "/views/components/prizes/prizes.php";

register_extended_field_group([
    "title"              => "Premi",
    "location"           => [Location::where("page_template", "=", "page-premi.php")],
    "fields"             => $prizes_fields,
    "style"              => "",
    "hide_on_screen"     => ["the_content"],
    "acfe_seamless_style" => 1,
]);
