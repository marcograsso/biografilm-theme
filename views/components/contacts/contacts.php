<?php

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
];
