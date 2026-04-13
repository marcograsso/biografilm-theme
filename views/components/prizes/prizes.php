<?php

use Extended\ACF\Fields\PostObject;
use Extended\ACF\Fields\Repeater;
use Extended\ACF\Fields\Select;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\WYSIWYGEditor;

return [
    Repeater::make("Sezioni", "sections")
        ->layout("block")
        ->collapsed("title")
        ->button("Aggiungi sezione")
        ->fields([
            Text::make("Titolo", "title"),
            Repeater::make("Premi", "awards")
                ->layout("block")
                ->collapsed("title")
                ->button("Aggiungi premio")
                ->fields([
                    Tab::make("Premio", "premio_tab")->placement("left"),
                    Select::make("Stato", "stato")
                        ->choices([
                            "assegnato"     => "Assegnato",
                            "non_assegnato" => "Non assegnato",
                        ])
                        ->default("assegnato"),
                    Text::make("Titolo", "title"),
                    Text::make("Sottotitolo", "subtitle"),

                    Tab::make("Motivazione", "motivazione_tab")->placement("left"),
                    Text::make("Titolo 2", "title_2"),
                    WYSIWYGEditor::make("Testo", "content")
                        ->toolbar(["bold", "italic", "link"])
                        ->tabs("all")
                        ->disableMediaUpload(),
                    Tab::make("Vincitore", "vincitore_tab")->placement("left"),
                    PostObject::make("Film", "film")
                        ->postTypes(["film"])
                        ->format("object")
                        ->nullable(),
                ]),
        ]),
];
