<?php

use Extended\ACF\Fields\Image;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\Textarea;

return [
    Tab::make("Contenuto", "contenuto_tab"),
    Textarea::make("Citazione", "quote")
        ->rows(4),
    Text::make("Autore", "author"),

    Tab::make("Immagine", "immagine_tab"),
    Image::make("Immagine", "image")->format("array"),
];
