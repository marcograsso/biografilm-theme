<?php

namespace App\Fields\Groups;

use Extended\ACF\Location;

register_extended_field_group([
    "title"              => "Programma",
    "location"           => [Location::where("page_template", "=", "page-programma.php")],
    "fields"             => [],
    "style"              => "",
    "hide_on_screen"     => ["the_content"],
    "acfe_seamless_style" => 1,
]);
