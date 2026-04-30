<?php

use Extended\ACF\Fields\Link;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\Textarea;
use Extended\ACF\Fields\TrueFalse;

return [
    Tab::make("Contenuto", "contenuto_tab"),
    Text::make("Titolo", "cta_title"),
    Textarea::make("Descrizione", "cta_desc")->rows(3),
    Link::make("Link", "cta_link"),

    Tab::make("Stile", "stile_tab"),
    TrueFalse::make("Bordo superiore", "border_top")->stylized()->column(50),
    TrueFalse::make("Bordo inferiore", "border_bottom")->stylized()->column(50),
    Tab::make("Impostazioni", "impostazioni_tab"),
    Text::make("Ancora (ID)", "anchor")
        ->helperText("ID per i link ancora. Inserisci senza il simbolo #.")
        ->placeholder("es: sezione-contatti")
        ->prefix("#")
        ->wrapper(["width" => 25]),
];
