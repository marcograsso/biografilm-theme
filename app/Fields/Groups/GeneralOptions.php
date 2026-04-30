<?php

use Extended\ACF\Location;
use Extended\ACF\Fields\Group;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\Textarea;
use Extended\ACF\Fields\Link;

register_extended_field_group([
    "title" => "Archivi",
    "location" => [Location::where("options_page", "theme-archivi")],
    "fields" => [
        Tab::make("Progetti"),
        Text::make("Titolo", "progetti_titolo")->helperText(
            "Titolo mostrato nella pagina archivio Progetti.",
        ),
        Textarea::make("Descrizione", "progetti_descrizione")->helperText(
            "Testo introduttivo mostrato nella pagina archivio Progetti.",
        ),
        Tab::make("Progetti CTA"),
        Text::make("Titolo", "progetti_cta_titolo")->helperText(
            "Titolo della CTA Progetti.",
        ),
        Textarea::make("Descrizione", "progetti_cta_descrizione")->helperText(
            "Testo della CTA Progetti.",
        ),
        Link::make("Link", "progetti_cta_link")->helperText(
            "Link della CTA Progetti.",
        ),
        Tab::make("Eventi"),
        Text::make("Titolo", "eventi_titolo")->helperText(
            "Titolo mostrato nella pagina archivio Eventi.",
        ),
        Textarea::make("Descrizione", "eventi_descrizione")->helperText(
            "Testo introduttivo mostrato nella pagina archivio Eventi.",
        ),
        Tab::make("Who's Coming"),
        Link::make("Documento", "whoscoming_documento")
            ->helperText(
                "Link al documento scaricabile dalla pagina archivio Who's Coming.",
            )
            ->format("array"),

        Tab::make("Contents Doc"),
        Group::make("Intro", "contents_doc_intro")
            ->layout("block")
            ->fields(
                require get_stylesheet_directory() .
                    "/views/components/text-displayer/text-displayer.php",
            ),

        Tab::make("Contents Drama"),
        Group::make("Intro", "contents_drama_intro")
            ->layout("block")
            ->fields(
                require get_stylesheet_directory() .
                    "/views/components/text-displayer/text-displayer.php",
            ),
    ],
    "style" => "default",
]);
