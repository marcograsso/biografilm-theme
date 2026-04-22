<?php

namespace App\Fields\Groups;

use Extended\ACF\Fields\FlexibleContent;
use Extended\ACF\Fields\Gallery;
use Extended\ACF\Fields\Layout;
use Extended\ACF\Fields\Link;
use Extended\ACF\Fields\Repeater;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\WYSIWYGEditor;
use Extended\ACF\Location;

register_extended_field_group([
    "title"    => "Approfondimenti",
    "style"    => "default",
    "location" => [
        Location::where("post_type", "news"),
        Location::where("post_type", "partner"),
    ],
    "fields"   => [
        FlexibleContent::make("Approfondimenti", "approfondimenti")
            ->button("Aggiungi contenuto")
            ->layouts([
                Layout::make("Testo", "text")
                    ->layout("block")
                    ->fields([
                        WYSIWYGEditor::make("Testo", "content")
                            ->toolbar(["bold", "italic", "link", "bullist", "numlist"])
                            ->tabs("all")
                            ->disableMediaUpload(),
                    ]),
                Layout::make("Links", "link")
                    ->layout("block")
                    ->fields([
                        Repeater::make("Links", "links")
                            ->layout("block")
                            ->collapsed("link")
                            ->button("Aggiungi link")
                            ->fields([
                                Link::make("Link", "link")->format("array"),
                            ]),
                    ]),
                Layout::make("Accordion", "accordion")
                    ->layout("block")
                    ->fields([
                        Repeater::make("Voci", "items")
                            ->layout("block")
                            ->collapsed("question")
                            ->button("Aggiungi voce")
                            ->fields([
                                Text::make("Domanda", "question"),
                                WYSIWYGEditor::make("Risposta", "answer")
                                    ->toolbar(["bold", "italic", "link", "bullist", "numlist"])
                                    ->tabs("all")
                                    ->disableMediaUpload(),
                            ]),
                    ]),
                Layout::make("Galleria", "gallery")
                    ->layout("block")
                    ->fields([
                        Gallery::make("Immagini", "images")->format("array"),
                    ]),
            ]),
    ],
]);
