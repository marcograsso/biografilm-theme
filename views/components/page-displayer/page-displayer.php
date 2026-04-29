<?php

use Extended\ACF\Fields\Group;
use Extended\ACF\Fields\Image;
use Extended\ACF\Fields\Link;
use Extended\ACF\Fields\Repeater;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\TrueFalse;
use Extended\ACF\Fields\WYSIWYGEditor;

return [
    Tab::make("Testi"),
    Text::make("Titolo", "title"),
    Link::make("Link", "link")->format("array"),
    Tab::make("Contenuto"),

    Repeater::make("Contenuti", "items")
        ->layout("block")
        ->collapsed("title")
        ->button("Aggiungi elemento")
        ->fields([
            Image::make("Immagine", "image")->format("array"),
            Text::make("Titolo", "title"),
            WYSIWYGEditor::make("Descrizione", "description")
                ->toolbar(["bold", "italic"])
                ->tabs("all")
                ->disableMediaUpload(),
            Link::make("Link", "link")->format("array"),
        ]),

    Tab::make("Stile"),
    Group::make("Bordi", "borders_group")
        ->layout("row")
        ->fields([
            TrueFalse::make("Titolo — bordo superiore", "border_top")
                ->stylized()
                ->default(false),
            TrueFalse::make("Titolo — bordo inferiore", "border_bottom")
                ->stylized()
                ->default(false),
            TrueFalse::make("Cards — bordo superiore", "cards_border_top")
                ->stylized()
                ->default(false),
            TrueFalse::make("Cards — bordo inferiore", "cards_border_bottom")
                ->stylized()
                ->default(false),
        ]),
];
