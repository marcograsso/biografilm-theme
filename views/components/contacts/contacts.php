<?php

use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\WYSIWYGEditor;

return [
    Text::make("Titolo", "title"),
    WYSIWYGEditor::make("Testo", "description")
        ->toolbar(["bold", "italic", "link"])
        ->tabs("all")
        ->disableMediaUpload(),
    Text::make("Shortcode", "shortcode")
        ->helperText("Incolla lo shortcode di Gravity Forms (es. [gravityforms id=\"1\"])"),
    Tab::make("Impostazioni", "impostazioni_tab"),
    Text::make("Ancora (ID)", "anchor")
        ->helperText("ID per i link ancora. Inserisci senza il simbolo #.")
        ->placeholder("es: sezione-contatti")
        ->prefix("#")
        ->wrapper(["width" => 25]),
];
