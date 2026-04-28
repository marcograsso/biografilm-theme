<?php

namespace App\PostTypes;

use Extended\ACF\ConditionalLogic;
use Extended\ACF\Fields\DatePicker;
use Extended\ACF\Fields\Group;
use Extended\ACF\Fields\Link;
use Extended\ACF\Fields\PostObject;
use Extended\ACF\Fields\Repeater;
use Extended\ACF\Fields\Select;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Taxonomy;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\Textarea;
use Extended\ACF\Fields\TrueFalse;
use Extended\ACF\Fields\WYSIWYGEditor;
use Extended\ACF\Location;

class Eventi extends \Timber\Post
{
    private static $names = [
        "singular" => "Evento",
        "plural" => "Eventi",
        "slug" => "evento",
    ];

    public static function register()
    {
        self::register_post_type();

        add_filter("timber/post/classmap", function ($classmap) {
            return array_merge($classmap, [
                self::$names["slug"] => self::class,
            ]);
        });

        add_action("pre_get_posts", [self::class, "order_archive_by_date"]);

        self::register_custom_fields();
    }

    public static function order_archive_by_date(\WP_Query $query): void
    {
        if (
            is_admin() ||
            !$query->is_main_query() ||
            !$query->is_post_type_archive(self::$names["slug"])
        ) {
            return;
        }

        $query->set("meta_query", [
            "relation" => "OR",
            "date_clause" => ["key" => "data_inizio", "compare" => "EXISTS"],
            "no_date" => ["key" => "data_inizio", "compare" => "NOT EXISTS"],
        ]);
        $query->set("orderby", ["date_clause" => "ASC"]);
    }

    private static function register_custom_fields()
    {
        $tabs_fields = require get_stylesheet_directory() .
            "/views/components/tabs/tabs.php";

        $tabs_fields[0] = $tabs_fields[0]->conditionalLogic([
            ConditionalLogic::where("usa_contenuti_progetto", "==", "0"),
        ]);

        register_extended_field_group([
            "title" => "Evento",
            "location" => [Location::where("post_type", self::$names["slug"])],
            "hide_on_screen" => ["the_content"],
            "style" => "default",
            "position" => "normal",
            "fields" => [
                Group::make("Evento", "evento")
                    ->layout("block")
                    ->fields([
                        Tab::make("Date", "date_tab"),
                        Select::make("Precisione date", "precisione_date")
                            ->choices([
                                "anno" => "Anno",
                                "anno_mese" => "Anno e mese",
                                "anno_mese_giorno" => "Anno, mese e giorno",
                            ])
                            ->default("anno_mese_giorno"),
                        DatePicker::make("Data inizio", "data_inizio")
                            ->helperText(
                                "Se la data è unica, compila questo campo e lascia vuoto 'Data fine'.",
                            )
                            ->displayFormat("d/m/Y")
                            ->format("Y-m-d"),
                        DatePicker::make("Data fine", "data_fine")
                            ->displayFormat("d/m/Y")
                            ->format("Y-m-d"),
                        Tab::make("Informazioni", "informazioni_tab"),
                        Taxonomy::make("Luogo", "luogo")
                            ->taxonomy("luogo-evento")
                            ->appearance("multi_select")
                            ->create(true)
                            ->save(true),
                        WYSIWYGEditor::make(
                            "Descrizione estesa",
                            "descrizione_estesa",
                        )
                            ->toolbar([
                                "bold",
                                "italic",
                                "link",
                                "bullist",
                                "numlist",
                            ])
                            ->tabs("all")
                            ->disableMediaUpload(),
                        Tab::make("Links", "links_tab"),
                        Repeater::make("Links", "links")
                            ->layout("block")
                            ->collapsed("link")
                            ->button("Aggiungi link")
                            ->fields([
                                Link::make("Link", "link")->format("array"),
                                Select::make("Stile", "stile")
                                    ->choices([
                                        "primary" => "Bottone primario",
                                        "secondary" => "Bottone secondario",
                                        "link" => "Link",
                                    ])
                                    ->default("primary"),
                            ]),
                        Tab::make("Contenuti", "contenuti_tab"),
                        PostObject::make("Progetto correlato", "progetto_correlato")
                            ->postTypes(["progetto"])
                            ->format("object")
                            ->helperText("Collega questo evento a un progetto del Campus."),
                        TrueFalse::make("Usa contenuti del progetto", "usa_contenuti_progetto")
                            ->stylized()
                            ->default(false)
                            ->helperText("Se attivo, i contenuti delle tab vengono ereditati dal progetto correlato."),
                        ...$tabs_fields,
                        Tab::make("Tassonomie", "tassonomie_tab"),
                        Taxonomy::make("Tipologia", "tipologia")
                            ->taxonomy("tipologia-evento")
                            ->appearance("multi_select")
                            ->create(true)
                            ->save(true),
                    ]),
            ],
        ]);

        register_extended_field_group([
            "title" => "Galleria",
            "key" => "group_eventi_galleria",
            "location" => [Location::where("post_type", self::$names["slug"])],
            "hide_on_screen" => ["the_content"],
            "style" => "default",
            "position" => "normal",
            "fields" => [
                Group::make("Galleria", "galleria_evento")
                    ->layout("block")
                    ->fields(
                        require get_stylesheet_directory() .
                            "/views/components/media-displayer/media-displayer.php",
                    ),
            ],
        ]);

        register_extended_field_group([
            "title" => "Partners",
            "key" => "group_eventi_partners",
            "location" => [Location::where("post_type", self::$names["slug"])],
            "hide_on_screen" => ["the_content"],
            "style" => "default",
            "position" => "normal",
            "fields" => [
                Group::make("Partners", "partners_displayer_evento")
                    ->layout("block")
                    ->fields(
                        require get_stylesheet_directory() .
                            "/views/components/partners-displayer/partners-displayer.php",
                    ),
            ],
        ]);
    }

    private static function register_post_type()
    {
        $name = self::$names["slug"];
        $names = self::$names;
        $args = [
            "has_archive" => "campus/eventi",
            "rewrite" => ["slug" => "campus/evento", "with_front" => false],
            "menu_icon" => "dashicons-calendar-alt",
            "menu_position" => null,
            "supports" => ["title", "thumbnail"],
            "labels" => [
                "name" => "Eventi",
                "singular_name" => "Evento",
                "add_new" => "Aggiungi nuovo",
                "add_new_item" => "Aggiungi nuovo evento",
                "edit_item" => "Modifica evento",
                "new_item" => "Nuovo evento",
                "view_item" => "Visualizza evento",
                "search_items" => "Cerca eventi",
                "not_found" => "Nessun evento trovato",
                "not_found_in_trash" => "Nessun evento nel cestino",
                "all_items" => "Tutti gli eventi",
            ],
        ];

        register_extended_post_type($name, $args, $names);
    }
}
