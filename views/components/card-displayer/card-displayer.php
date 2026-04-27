<?php

use Extended\ACF\Fields\Group;
use Extended\ACF\Fields\Link;
use Extended\ACF\Fields\Relationship;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\TrueFalse;

return [
    Text::make("Titolo", "title"),
    Link::make("Link", "link")->format("array"),
    Relationship::make("Contenuti", "items")
        ->postTypes(["film", "proiezione", "news", "progetto"])
        ->format("object")
        ->withSettings(["allow_duplicates" => 1]),
    TrueFalse::make("Sempre pari", "always_even")
        ->stylized()
        ->helperText("Se attivo e il numero di contenuti è dispari, l'ultimo elemento viene nascosto nelle griglie a 2 colonne e mostrato solo dalla griglia a 4 colonne (3xl)."),
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
            TrueFalse::make("Celle vuote — bordo inferiore", "filler_border_bottom")
                ->stylized()
                ->default(false),
        ]),
];
