<?php

namespace App\Fields\Groups;

use Extended\ACF\Location;

$locations_fields = require get_stylesheet_directory() .
    "/views/components/locations/locations.php";

register_extended_field_group([
    "title" => "Locations",
    "location" => [Location::where("page_template", "=", "page-locations.php")],
    "fields" => $locations_fields,
    "style" => "",
    "hide_on_screen" => ["the_content"],
    "acfe_seamless_style" => 1,
]);
