<?php

use Extended\ACF\Fields\Group;
use Extended\ACF\Fields\Link;
use Extended\ACF\Fields\Relationship;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\TrueFalse;

return [
    Tab::make("Testi"),
    Text::make("Titolo", "title"),
    Link::make("Link", "link")->format("array"),

    Tab::make("Contenuto"),
    Relationship::make("Contents", "items")
        ->postTypes(["contents-doc"])
        ->filters(["search"])
        ->elements(["featured_image"])
        ->minPosts(1),

    Tab::make("Stile"),
    Group::make("Bordi", "borders_group")
        ->layout("row")
        ->fields([
            TrueFalse::make("Intestazione — bordo superiore", "header_border_top")
                ->stylized()
                ->default(false),
            TrueFalse::make("Intestazione — bordo inferiore", "header_border_bottom")
                ->stylized()
                ->default(true),
            TrueFalse::make("Cards — bordo superiore", "cards_border_top")
                ->stylized()
                ->default(false),
            TrueFalse::make("Cards — bordo inferiore", "cards_border_bottom")
                ->stylized()
                ->default(false),
        ]),

    Tab::make("Impostazioni", "impostazioni_tab"),
    Text::make("Ancora (ID)", "anchor")
        ->helperText("ID per i link ancora. Inserisci senza il simbolo #.")
        ->placeholder("es: sezione-contatti")
        ->prefix("#")
        ->wrapper(["width" => 25]),
];
