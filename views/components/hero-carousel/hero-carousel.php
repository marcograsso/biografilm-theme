<?php

use Extended\ACF\Fields\ColorPicker;
use Extended\ACF\Fields\Image;
use Extended\ACF\Fields\Link;
use Extended\ACF\Fields\Repeater;
use Extended\ACF\Fields\Select;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\WYSIWYGEditor;
use Extended\ACF\Location;

register_extended_field_group([
    "title" => "Hero Carousel",
    "location" => [Location::where("page_type", "=", "front_page")],
    "fields" => [
        Repeater::make("Slide", "hero_carousel_slides")
            ->layout("block")
            ->collapsed("title")
            ->fields([
                Tab::make("Contenuto")->placement("left"),
                Image::make("Logo", "logo")->format("array"),
                Text::make("Titolo", "title"),
                Text::make("Sottotitolo", "subtitle"),
                WYSIWYGEditor::make("Descrizione", "description")
                    ->toolbar(["bold", "italic"])
                    ->tabs("all")
                    ->disableMediaUpload(),
                Link::make("Link", "link")->format("array"),
                Tab::make("Immagine")->placement("left"),
                Image::make("Immagine di sfondo", "background_image")->format(
                    "array",
                ),
                Tab::make("Stile")->placement("left"),
                ColorPicker::make("Colore testo", "text_color")->default(
                    "#ffffff",
                ),
                Select::make("Larghezza testo", "text_width")
                    ->choices([
                        "w-1/3" => "1/3 colonna",
                        "w-1/2" => "1/2 colonna",
                    ])
                    ->default("w-1/2"),
            ])
            ->button("Aggiungi slide"),
    ],
    "style" => "",
    "hide_on_screen" => ["the_content"],
]);
