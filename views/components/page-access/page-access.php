<?php

use Extended\ACF\Fields\Image;
use Extended\ACF\Fields\Link;
use Extended\ACF\Fields\Repeater;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\TrueFalse;
use Extended\ACF\Fields\WYSIWYGEditor;

return [
    Repeater::make("Sezioni", "items")
        ->layout("block")
        ->collapsed("title")
        ->button("Aggiungi sezione")
        ->fields([
            Tab::make("Contenuto", "contenuto_tab")->placement("left"),
            Text::make("Titolo", "title"),
            WYSIWYGEditor::make("Descrizione", "description")
                ->toolbar(["bold", "italic", "link"])
                ->tabs("all")
                ->disableMediaUpload(),
            Link::make("Link", "link")->format("array"),

            Tab::make("Logo e sfondo", "logo_sfondo_tab")->placement("left"),
            Image::make("Logo", "logo")->format("array")->helperText("Per un risultato ottimale, usa un'immagine quadrata."),
            Image::make("Sfondo", "background")->format("array"),

            Tab::make("Stile", "stile_tab")->placement("left"),
            TrueFalse::make("Bordo superiore", "border_top")
                ->stylized()
                ->default(false),
            TrueFalse::make("Bordo inferiore", "border_bottom")
                ->stylized()
                ->default(false),
            TrueFalse::make("Nascondi sfondo su desktop", "hide_background_desktop")
                ->stylized()
                ->default(false),
        ]),
];
