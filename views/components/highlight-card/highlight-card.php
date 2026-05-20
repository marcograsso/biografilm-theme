<?php

use Extended\ACF\Fields\Image;
use Extended\ACF\Fields\PostObject;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Text;

return [
    Tab::make("Contenuti", "contenuti_tab"),
    PostObject::make("Contenuto in evidenza", "post")
        ->postTypes(["film", "proiezione", "progetto", "evento"])
        ->format("object"),
    Image::make("Immagine alternativa", "alt_image")
        ->helperText("Sovrascrive l'immagine del contenuto selezionato.")
        ->format("array")
        ->wrapper(["width" => 50]),
    Tab::make("Impostazioni", "impostazioni_tab"),
    Text::make("Ancora (ID)", "anchor")
        ->helperText("ID per i link ancora. Inserisci senza il simbolo #.")
        ->placeholder("es: sezione-contatti")
        ->prefix("#")
        ->wrapper(["width" => 25]),
];
