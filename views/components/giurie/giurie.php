<?php

use Extended\ACF\Fields\Image;
use Extended\ACF\Fields\Repeater;
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
            WYSIWYGEditor::make("Sottotitolo", "subtitle")
                ->toolbar(["bold", "italic", "link"])
                ->tabs("all")
                ->disableMediaUpload(),
            Repeater::make("Giurati", "members")
                ->layout("block")
                ->collapsed("title")
                ->button("Aggiungi giurato")
                ->fields([
                    Tab::make("Giurato", "giurato_tab")->placement("left"),
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
        ]),
];
