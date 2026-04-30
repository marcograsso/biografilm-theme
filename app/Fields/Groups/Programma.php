<?php

namespace App\Fields\Groups;

use Extended\ACF\Fields\Select;
use Extended\ACF\Location;

register_extended_field_group([
    "title"              => "Programma",
    "location"           => [Location::where("page_template", "=", "page-programma.php")],
    "fields"             => [
        Select::make("Tipo programma", "tipo_programma")
            ->choices([
                "festival" => "Programma Festival",
                "doc"      => "Programma Doc",
                "drama"    => "Programma Drama",
            ])
            ->default("festival")
            ->wrapper(["width" => 33]),
    ],
    "style"              => "",
    "hide_on_screen"     => ["the_content"],
    "acfe_seamless_style" => 1,
]);
