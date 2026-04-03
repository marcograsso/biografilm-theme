<?php

use Extended\ACF\Fields\Link;
use Extended\ACF\Fields\Relationship;
use Extended\ACF\Fields\Text;

return [
    Text::make("Titolo", "title"),
    Link::make("Link", "link")->format("array"),
    Relationship::make("Contenuti", "items")
        ->postTypes(["proiezione", "news"])
        ->format("object")
        ->withSettings(["allow_duplicates" => 1]),
];
