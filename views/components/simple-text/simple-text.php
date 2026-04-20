<?php

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
        ->toolbar(["bold", "italic", "link", "bullist", "numlist"])
        ->tabs("all")
        ->disableMediaUpload(),

    Tab::make("Stile", "stile_tab"),
    TrueFalse::make("Bordo superiore", "border_top")
        ->stylized()
        ->column(50),
    TrueFalse::make("Bordo inferiore", "border_bottom")
        ->stylized()
        ->column(50),
];
