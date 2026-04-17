<?php

use Extended\ACF\Fields\Relationship;
use Extended\ACF\Fields\Text;

return [
    Text::make("Titolo", "title"),
    Text::make("Sottotitolo", "subtitle"),
    Relationship::make("Film", "items")
        ->postTypes(["film"])
        ->format("object")
        ->withSettings(["allow_duplicates" => 1]),
];
