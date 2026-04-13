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
    Select::make("Dimensione heading", "title_size")
        ->choices([
            "big" => "Grande uppercase",
            "normal" => "Normale",
        ])
        ->default("normal"),
    TrueFalse::make("Mostra bordi", "show_borders")
        ->stylized()
        ->default(false),
];
