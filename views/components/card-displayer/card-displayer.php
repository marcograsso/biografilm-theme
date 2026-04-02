<?php

use Extended\ACF\Fields\Relationship;
use Extended\ACF\Fields\Text;

return [
    Text::make("Titolo", "title"),
    Relationship::make("Proiezioni", "proiezioni")
        ->postTypes(["proiezione"])
        ->format("object")
        ->withSettings(["allow_duplicates" => 1]),
];
