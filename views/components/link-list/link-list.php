<?php

use Extended\ACF\ConditionalLogic;
use Extended\ACF\Fields\File;
use Extended\ACF\Fields\Link;
use Extended\ACF\Fields\Repeater;
use Extended\ACF\Fields\Select;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Text;

return [
    Tab::make("Intestazione", "intestazione_tab"),
    Text::make("Titolo", "heading"),
    Text::make("Sottotitolo", "subtitle"),

    Tab::make("Link", "links_tab"),
    Repeater::make("Link", "links")
        ->layout("block")
        ->collapsed("title")
        ->button("Aggiungi link")
        ->fields([
            Text::make("Titolo", "title")
                ->helperText("Testo visualizzato nel blocco. Obbligatorio.")
                ->column(50),
            Select::make("Tipo", "tipo")
                ->choices([
                    "link" => "Link",
                    "document" => "Documento",
                ])
                ->default("link")
                ->helperText("Scegli se il blocco apre un link o scarica un documento.")
                ->column(50),
            Link::make("Link", "link")
                ->format("array")
                ->helperText("URL di destinazione. Il titolo del link non viene usato se il campo Titolo è compilato.")
                ->conditionalLogic([
                    ConditionalLogic::where("tipo", "==", "link"),
                ]),
            File::make("Documento", "document")
                ->format("array")
                ->helperText("Carica un file (es. PDF). Si aprirà in una nuova scheda.")
                ->conditionalLogic([
                    ConditionalLogic::where("tipo", "==", "document"),
                ]),
        ]),
];
