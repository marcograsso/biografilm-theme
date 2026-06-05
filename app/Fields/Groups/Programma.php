<?php

namespace App\Fields\Groups;

use Extended\ACF\ConditionalLogic;
use Extended\ACF\Fields\DatePicker;
use Extended\ACF\Fields\Repeater;
use Extended\ACF\Fields\Select;
use Extended\ACF\Fields\Link;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\TimePicker;
use Extended\ACF\Fields\WYSIWYGEditor;
use Extended\ACF\Location;

register_extended_field_group([
    "title" => "Programma",
    "location" => [Location::where("page_template", "=", "page-programma.php")],
    "fields" => [
        Link::make("Documento", "archive_documento")
            ->key("field_programma_archive_documento")
            ->helperText("Link al documento scaricabile dalla pagina programma. Opzionale.")
            ->format("array"),

        Select::make("Tipo programma", "tipo_programma")
            ->key("field_programma_tipo_programma")
            ->choices([
                "festival" => "Programma Festival",
                "doc" => "Programma Compilabile",

            ])
            ->default("festival")
            ->wrapper(["width" => 33]),

        Repeater::make("Giorni", "programma_doc_giorni")
            ->key("field_programma_doc_giorni")
            ->layout("block")
            ->collapsed("field_programma_doc_giorni_giorno")
            ->conditionalLogic([
                ConditionalLogic::where("tipo_programma", "==", "doc", null, "field_programma_tipo_programma"),
            ])
            ->fields([
                DatePicker::make("Giorno", "giorno")
                    ->key("field_programma_doc_giorni_giorno")
                    ->displayFormat("d/m/Y")
                    ->format("Y-m-d"),

                Repeater::make("Eventi", "eventi")
                    ->key("field_programma_doc_giorni_eventi")
                    ->layout("block")
                    ->collapsed("field_programma_doc_giorni_eventi_titolo")
                    ->fields([
                        Tab::make("Info", "info_tab")
                            ->key("field_programma_doc_giorni_eventi_info_tab"),
                        TimePicker::make("Orario", "orario")
                            ->key("field_programma_doc_giorni_eventi_orario")
                            ->displayFormat("H:i")
                            ->format("H:i")
                            ->wrapper(["width" => 20]),
                        Text::make("Titolo", "titolo")
                            ->key("field_programma_doc_giorni_eventi_titolo")
                            ->wrapper(["width" => 80]),
                        Text::make("Location (testo)", "location_text")
                            ->key("field_programma_doc_giorni_eventi_location_text")
                            ->wrapper(["width" => 50]),
                        Link::make("Location (con link)", "location")
                            ->key("field_programma_doc_giorni_eventi_location")
                            ->wrapper(["width" => 50]),
                        Tab::make("Descrizione", "descrizione_tab")
                            ->key("field_programma_doc_giorni_eventi_descrizione_tab"),
                        WYSIWYGEditor::make("Descrizione", "descrizione")
                            ->key("field_programma_doc_giorni_eventi_descrizione")
                            ->tabs("visual")
                            ->disableMediaUpload()
                            ->wrapper(["width" => 100]),
                    ]),
            ]),
    ],
    "style" => "",
    "hide_on_screen" => ["the_content"],
    "acfe_seamless_style" => 1,
]);
