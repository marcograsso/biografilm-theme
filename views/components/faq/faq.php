<?php

use Extended\ACF\Fields\Repeater;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\WYSIWYGEditor;

return [
    Tab::make("Intestazione", "intestazione_tab"),
    Text::make("Titolo", "heading"),
    Text::make("Sottotitolo", "subtitle"),

    Tab::make("FAQ", "faq_tab"),
    Repeater::make("FAQ", "faqs")
        ->layout("block")
        ->collapsed("question")
        ->button("Aggiungi FAQ")
        ->fields([
            Text::make("Domanda", "question"),
            WYSIWYGEditor::make("Risposta", "answer")
                ->toolbar(["bold", "italic", "link", "bullist", "numlist"])
                ->tabs("all")
                ->disableMediaUpload()
                ->withSettings(["acfe_wysiwyg_height" => 200]),
        ]),
    Tab::make("Impostazioni", "impostazioni_tab"),
    Text::make("Ancora (ID)", "anchor")
        ->helperText("ID per i link ancora. Inserisci senza il simbolo #.")
        ->placeholder("es: sezione-contatti")
        ->prefix("#")
        ->wrapper(["width" => 25]),
];
