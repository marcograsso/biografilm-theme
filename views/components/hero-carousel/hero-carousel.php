<?php

use Extended\ACF\Fields\ColorPicker;
use Extended\ACF\Fields\File;
use Extended\ACF\Fields\Image;
use Extended\ACF\Fields\Link;
use Extended\ACF\Fields\Repeater;
use Extended\ACF\Fields\Select;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\WYSIWYGEditor;

return [
    Repeater::make("Slide", "hero_carousel_slides")
        ->layout("block")
        ->collapsed("title")
        ->button("Aggiungi slide")
        ->fields([
            Tab::make("Immagine")->placement("left"),
            Image::make("Immagine di sfondo", "background_image")->format(
                "array",
            ),
            File::make("Video di sfondo", "background_video")->format("array"),
            Tab::make("Testi")->placement("left"),
            Text::make("Titolo", "title"),
            Text::make("Sottotitolo", "subtitle"),
            WYSIWYGEditor::make("Descrizione", "description")
                ->toolbar(["bold", "italic"])
                ->tabs("all")
                ->disableMediaUpload()
                ->withSettings(["acfe_wysiwyg_height" => 60]),

            Tab::make("Extra")->placement("left"),
            Link::make("Link", "link")->format("array"),
            Image::make("Logo superiore", "logo")->format("array"),

            Tab::make("Stile")->placement("left"),
            ColorPicker::make("Colore testo", "text_color")
                ->default("#ffffff")
                ->palette(["#ffffff", "#1e1e1e", "#bd1822"]),
            Select::make("Larghezza testo", "text_width")
                ->choices([
                    "w-1/3" => "1/3 colonna",
                    "w-1/2" => "1/2 colonna",
                ])
                ->default("w-1/2"),
            Select::make("Stile titolo", "title_style")
                ->choices([
                    "big" => "Grande uppercase",
                    "medium" => "Medium",
                    "normal" => "Normale",
                ])
                ->default("normal"),
        ]),
    Tab::make("Impostazioni", "impostazioni_tab"),
    Text::make("Ancora (ID)", "anchor")
        ->helperText("ID per i link ancora. Inserisci senza il simbolo #.")
        ->placeholder("es: sezione-contatti")
        ->prefix("#")
        ->wrapper(["width" => 25]),
];
