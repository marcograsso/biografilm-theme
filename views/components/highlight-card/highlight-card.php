<?php

use Extended\ACF\Fields\PostObject;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Text;

return [
    PostObject::make("Contenuto in evidenza", "post")
        ->postTypes(["film", "proiezione", "progetto", "evento"])
        ->format("object"),
    Tab::make("Impostazioni", "impostazioni_tab"),
    Text::make("Ancora (ID)", "anchor")
        ->helperText("ID per i link ancora. Inserisci senza il simbolo #.")
        ->placeholder("es: sezione-contatti")
        ->prefix("#")
        ->wrapper(["width" => 25]),
];
