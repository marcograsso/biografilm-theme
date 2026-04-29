<?php

use Extended\ACF\Fields\Link;
use Extended\ACF\Fields\Repeater;
use Extended\ACF\Fields\Select;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\WYSIWYGEditor;

return [
    Tab::make("Intestazione", "intestazione_tab"),
    Text::make("Titolo", "heading"),
    Text::make("Sottotitolo", "subtitle"),

    Tab::make("Biglietti", "biglietti_tab"),
    Repeater::make("Biglietti", "tickets")
        ->layout("block")
        ->collapsed("name")
        ->button("Aggiungi biglietto")
        ->fields([
            Tab::make("Biglietto standard", "biglietto_tab"),
            WYSIWYGEditor::make("Nome", "name")

                ->toolbar(["bold", "italic", "link"])
                ->tabs("all")
                ->disableMediaUpload()
                ->withSettings(["acfe_wysiwyg_height" => 60]),
            Text::make("Prezzo", "price"),

            Tab::make("Sub-biglietti", "sub_biglietti_tab"),
            Repeater::make("Sub-biglietti", "sub_tickets")
                ->layout("block")
                ->collapsed("name")
                ->button("Aggiungi sub-biglietto")
                ->fields([
                    WYSIWYGEditor::make("Nome", "name")
                        ->toolbar(["bold", "italic", "link"])
                        ->tabs("all")
                        ->disableMediaUpload()
                        ->withSettings(["acfe_wysiwyg_height" => 60])
                        ->column(70),
                    Text::make("Prezzo", "price")->column(30),
                ]),

            Tab::make("Descrizione", "description_tab"),
            Text::make("Titolo", "description_title")->column(50),
            WYSIWYGEditor::make("Descrizione", "description")
                ->toolbar(["bold", "italic", "link", "bullist", "numlist"])
                ->tabs("all")
                ->disableMediaUpload()
                ->withSettings(["acfe_wysiwyg_height" => 300]),
        ]),

    Tab::make("Link", "links_tab"),
    Repeater::make("Link", "links")
        ->layout("block")
        ->collapsed("link")
        ->button("Aggiungi link")
        ->fields([
            Link::make("Link", "link")->format("array")->column(50),
            Select::make("Stile", "style")
                ->choices([
                    "default" => "Primary",
                    "secondary" => "Secondary",
                    "link" => "Link",
                ])
                ->default("default")
                ->column(50),
        ]),
];
