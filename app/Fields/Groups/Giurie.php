<?php

namespace App\Fields\Groups;

use Extended\ACF\Location;

$giurie_fields = require get_stylesheet_directory() . "/views/components/giurie/giurie.php";

register_extended_field_group([
    "title"               => "Giurie",
    "location"            => [Location::where("page_template", "=", "page-giurie.php")],
    "fields"              => $giurie_fields,
    "style"               => "",
    "hide_on_screen"      => ["the_content"],
    "acfe_seamless_style" => 1,
]);
