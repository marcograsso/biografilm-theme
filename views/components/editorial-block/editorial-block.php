<?php

use Extended\ACF\Fields\Image;
use Extended\ACF\Fields\Select;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\WYSIWYGEditor;

return [
    Tab::make("Heading", "heading_tab"),
    Text::make("Heading", "heading"),

    Tab::make("Titolo", "title_tab"),
    Text::make("Titolo", "title"),

    Tab::make("Contenuto", "content_tab"),
    WYSIWYGEditor::make("Testo", "content")
        ->toolbar(["bold", "italic", "link", "bullist", "numlist"])
        ->tabs("all")
        ->disableMediaUpload(),

    Tab::make("Immagine", "immagine_tab"),
    Image::make("Immagine", "image")->format("array"),

    Tab::make("Stile", "stile_tab"),
    Select::make("Dimensione heading", "title_size")
        ->choices([
            "big" => "Grande uppercase",
            "normal" => "Normale",
        ])
        ->default("normal"),
    Select::make("Posizione immagine", "image_position")
        ->choices([
            "left" => "Sinistra",
            "right" => "Destra",
        ])
        ->default("left"),
    Select::make("Proporzioni", "column_layout")
        ->choices([
            "60_40" => "60 / 40",
            "40_60" => "40 / 60",
            "50_50" => "50 / 50",
        ])
        ->default("60_40"),
];
