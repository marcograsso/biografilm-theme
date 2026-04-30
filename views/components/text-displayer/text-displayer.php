<?php

use Extended\ACF\Fields\Select;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\TrueFalse;
use Extended\ACF\Fields\WYSIWYGEditor;

return [
    Tab::make("Heading", "heading_tab"),
    Text::make("Heading", "heading"),

    Tab::make("Titolo", "title_tab"),
    Text::make("Titolo", "title"),

    Tab::make("Contenuto", "content_tab"),
    WYSIWYGEditor::make("Testo", "content")
        ->toolbar(["bold", "italic", "link"])
        ->tabs("all")
        ->disableMediaUpload(),

    Tab::make("Stile", "stile_tab"),
    Select::make("Dimensione heading", "heading_size")
        ->choices([
            "big" => "Grande uppercase",
            "medium" => "Medium",
            "normal" => "Normale",
        ])
        ->default("normal"),
    Select::make("Dimensione titolo", "title_size")
        ->choices([
            "big" => "Grande",
            "medium" => "Medium",
            "normal" => "Normale",
        ])
        ->default("normal"),
    Select::make("Layout colonne", "column_layout")
        ->choices([
            "3_col" => "3 colonne (1/3 + 2/3)",
            "40_60" => "40 / 60",
        ])
        ->default("3_col"),
    Select::make("Direzione", "direction")
        ->choices([
            "ltr" => "Heading a sinistra",
            "rtl" => "Heading a destra",
        ])
        ->default("ltr"),
    TrueFalse::make("Mostra bordo superiore", "show_border_top")
        ->stylized()
        ->default(false),
    TrueFalse::make("Mostra bordo inferiore", "show_border_bottom")
        ->stylized()
        ->default(false),
    Tab::make("Impostazioni", "impostazioni_tab"),
    Text::make("Ancora (ID)", "anchor")
        ->helperText("ID per i link ancora. Inserisci senza il simbolo #.")
        ->placeholder("es: sezione-contatti")
        ->prefix("#")
        ->wrapper(["width" => 25]),
];
