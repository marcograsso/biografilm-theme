<?php

use Extended\ACF\Location;

$hero_fields = require get_stylesheet_directory() . "/views/components/hero/hero.php";

register_extended_field_group([
    "title"    => "Hero",
    "location" => [
        Location::where("post_type", "page"),
        Location::where("post_type", "sezione"),
    ],
    "fields"   => $hero_fields,
    "position" => "acf_after_title",
    "style"    => "default",
]);
