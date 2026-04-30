<?php

use Extended\ACF\Fields\Image;
use Extended\ACF\Fields\Repeater;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\WYSIWYGEditor;

return [
    Tab::make("Titolo", "titolo_tab"),
    Text::make("Titolo", "title"),
    Tab::make("Membri", "membri_tab"),
    Repeater::make("Membri", "members")
        ->layout("block")
        ->collapsed("title")
        ->button("Aggiungi membro")
        ->fields([
            Tab::make("Membro", "membro_tab")->placement("left"),
            Text::make("Nome", "title"),
            Text::make("Ruolo", "subtitle"),
            Text::make("Provenienza", "provenienza"),

            Tab::make("Biografia", "biografia_tab")->placement("left"),
            WYSIWYGEditor::make("Biografia", "content")
                ->toolbar(["bold", "italic", "link"])
                ->tabs("all")
                ->disableMediaUpload(),

            Tab::make("Immagine", "immagine_tab")->placement("left"),
            Image::make("Foto", "image")->format("array"),
        ]),
    Tab::make("Impostazioni", "impostazioni_tab"),
    Text::make("Ancora (ID)", "anchor")
        ->helperText("ID per i link ancora. Inserisci senza il simbolo #.")
        ->placeholder("es: sezione-contatti")
        ->prefix("#")
        ->wrapper(["width" => 25]),
];
