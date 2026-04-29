<?php

use Extended\ACF\Fields\Group;
use Extended\ACF\Fields\Link;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Taxonomy;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\TrueFalse;

return [
    Tab::make("Testi"),
    Text::make("Titolo", "title"),
    Link::make("Link", "link")->format("array"),
    Tab::make("Contenuto"),
    Taxonomy::make("Tipologie", "tipologie")
        ->taxonomy("accredito-whos-coming")
        ->appearance("multi_select")
        ->format("id")
        ->helperText("Filtra le cards per tipologia di accredito. Lascia vuoto per mostrare tutti."),
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
];
