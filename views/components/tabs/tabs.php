<?php

use Extended\ACF\Fields\FlexibleContent;
use Extended\ACF\Fields\Layout;
use Extended\ACF\Fields\Link;
use Extended\ACF\Fields\Repeater;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\WYSIWYGEditor;

return [
    Repeater::make("Tab", "tabs")
        ->layout("block")
        ->collapsed("title")
        ->button("Aggiungi tab")
        ->fields([
            Text::make("Titolo", "title"),
            FlexibleContent::make("Contenuti", "contents")
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
                ]),
        ]),
];
