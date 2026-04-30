<?php

use Extended\ACF\Fields\Relationship;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Text;

return [
    Text::make("Titolo", "title"),
    Text::make("Sottotitolo", "subtitle"),
    Relationship::make("Film", "items")
        ->postTypes(["film"])
        ->format("object")
        ->withSettings(["allow_duplicates" => 1]),
    Tab::make("Impostazioni", "impostazioni_tab"),
    Text::make("Ancora (ID)", "anchor")
        ->helperText("ID per i link ancora. Inserisci senza il simbolo #.")
        ->placeholder("es: sezione-contatti")
        ->prefix("#")
        ->wrapper(["width" => 25]),
];
