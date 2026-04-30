<?php

use Extended\ACF\ConditionalLogic;
use Extended\ACF\Fields\Group;
use Extended\ACF\Fields\Link;
use Extended\ACF\Fields\PostObject;
use Extended\ACF\Fields\Repeater;
use Extended\ACF\Fields\Select;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\TrueFalse;

return [
    Tab::make("Titolo", "titolo_tab"),
    Text::make("Titolo", "title"),
    Text::make("Sottotitolo", "subtitle")->conditionalLogic([
        ConditionalLogic::where("template", "==", "righe"),
    ]),
    Link::make("Link", "link")->format("array"),

    Tab::make("Partners", "partners_tab"),
    Repeater::make("Partners", "items")
        ->layout("table")
        ->button("Aggiungi partner")
        ->fields([
            PostObject::make("Partner", "partner")
                ->postTypes(["partner"])
                ->format("object"),
            TrueFalse::make("Doppia larghezza", "wide")->stylized(),
        ]),

    Tab::make("Template", "template_tab"),
    Select::make("Template", "template")
        ->choices([
            "colonne" => "Colonne",
            "righe" => "Righe",
        ])
        ->default("colonne"),

    Tab::make("Stile", "stile_tab"),
    Select::make("Stile titolo", "title_style")
        ->choices([
            "display-h2" => "Grande",
            "heading-h4" => "Medio",
            "heading-h5" => "Piccolo",
        ])
        ->default("display-h2"),
    Group::make("Bordi", "borders_group")
        ->layout("row")
        ->fields([
            TrueFalse::make("Titolo — bordo superiore", "border_top")
                ->stylized()
                ->default(false),
            TrueFalse::make("Titolo — bordo inferiore", "border_bottom")
                ->stylized()
                ->default(false),
            TrueFalse::make("Cards — bordo superiore", "cards_border_top")
                ->stylized()
                ->default(false),
            TrueFalse::make("Cards — bordo inferiore", "cards_border_bottom")
                ->stylized()
                ->default(false),
            TrueFalse::make(
                "Celle vuote — bordo inferiore",
                "filler_border_bottom",
            )
                ->stylized()
                ->default(false),
        ]),
    Tab::make("Impostazioni", "impostazioni_tab"),
    Text::make("Ancora (ID)", "anchor")
        ->helperText("ID per i link ancora. Inserisci senza il simbolo #.")
        ->placeholder("es: sezione-contatti")
        ->prefix("#")
        ->wrapper(["width" => 25]),
];
