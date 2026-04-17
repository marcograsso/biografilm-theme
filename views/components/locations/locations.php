<?php

use App\FieldTypes\Mapbox;
use Extended\ACF\Fields\Image;
use Extended\ACF\Fields\Link;
use Extended\ACF\Fields\Repeater;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\Tab;

return [
    Text::make("Titolo", "heading"),
    Text::make("Sottotitolo", "subtitle"),
    Repeater::make("Locations", "locations")
        ->layout("block")
        ->collapsed("name")
        ->button("Aggiungi location")
        ->fields([
            Tab::make("Card"),
            Text::make("Nome", "name")->column(50),
            Text::make("Indirizzo", "address")->column(50),
            Image::make("Immagine", "image")->format("array"),
            Link::make("Link", "link")->format("array"),
            Tab::make("Mappa"),
            Mapbox::make("Posizione su mappa", "map_location")
                ->mapbox_api_key(get_field("mapbox_api_key", "option") ?: "")
                ->default_country("it"),
        ]),
];
