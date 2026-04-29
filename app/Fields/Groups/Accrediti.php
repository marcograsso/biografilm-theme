<?php

namespace App\Fields\Groups;

use Extended\ACF\Fields\Group;
use Extended\ACF\Location;

$tickets_fields = require get_stylesheet_directory() .
    "/views/components/tickets/tickets.php";

register_extended_field_group([
    "title" => "Accrediti",
    "location" => [
        Location::where("post_slug", "==", "tickets"),
        Location::where("post_slug", "==", "tickets-industry"),
    ],
    "fields" => [
        Group::make("Accrediti", "accrediti")
            ->fields($tickets_fields),
    ],
    "style" => "",
    "hide_on_screen" => ["the_content"],
    "acfe_seamless_style" => 1,
]);
