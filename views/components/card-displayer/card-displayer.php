<?php

use Extended\ACF\Fields\Link;
use Extended\ACF\Fields\Relationship;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\TrueFalse;

return [
    Text::make("Titolo", "title"),
    Link::make("Link", "link")->format("array"),
    Relationship::make("Contenuti", "items")
        ->postTypes(["film", "proiezione", "news"])
        ->format("object")
        ->withSettings(["allow_duplicates" => 1]),
    TrueFalse::make("Sempre pari", "always_even")
        ->stylized()
        ->helperText("Se attivo e il numero di contenuti è dispari, l'ultimo elemento viene nascosto nelle griglie a 2 colonne e mostrato solo dalla griglia a 4 colonne (3xl)."),
];
