<?php

namespace App\PostTypes;

use Extended\ACF\Fields\DatePicker;
use Extended\ACF\Fields\Group;
use Extended\ACF\Fields\Link;
use Extended\ACF\Fields\Repeater;
use Extended\ACF\Fields\Select;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Taxonomy;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\Textarea;
use Extended\ACF\Fields\WYSIWYGEditor;
use Extended\ACF\Location;

class Progetti extends \Timber\Post
{
    private static $names = [
        "singular" => "Progetto",
        "plural" => "Progetti",
        "slug" => "progetto",
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
        if (is_admin() || !$query->is_main_query() || !$query->is_post_type_archive(self::$names["slug"])) {
            return;
        }

        $query->set("meta_query", [
            "relation"     => "OR",
            "date_clause"  => ["key" => "periodo_inizio", "compare" => "EXISTS"],
            "no_date"      => ["key" => "periodo_inizio", "compare" => "NOT EXISTS"],
        ]);
        $query->set("orderby", ["date_clause" => "ASC"]);
    }

    private static function register_custom_fields()
    {
        $tabs_fields = require get_stylesheet_directory() .
            "/views/components/tabs/tabs.php";

        register_extended_field_group([
            "title" => "Progetto",
            "location" => [Location::where("post_type", self::$names["slug"])],
            "hide_on_screen" => ["the_content"],
            "style" => "default",
            "position" => "normal",
            "fields" => [
                Group::make("Progetto", "progetto")
                    ->layout("block")
                    ->fields([
                        Tab::make("Generali"),
                        Textarea::make("Descrizione breve", "descrizione_breve")
                            ->rows(4)
                            ->helperText(
                                "Questo testo verrà utilizzato come anteprima nelle card del progetto.",
                            ),
                        Tab::make("Date", "date_tab"),
                        Select::make("Precisione date", "precisione_date")
                            ->choices([
                                "anno"             => "Anno",
                                "anno_mese"        => "Anno e mese",
                                "anno_mese_giorno" => "Anno, mese e giorno",
                            ])
                            ->default("anno_mese_giorno"),
                        DatePicker::make("Periodo inizio", "periodo_inizio")
                            ->displayFormat("d/m/Y")
                            ->format("Y-m-d"),
                        DatePicker::make("Periodo fine", "periodo_fine")
                            ->displayFormat("d/m/Y")
                            ->format("Y-m-d"),
                        Tab::make("Informazioni", "informazioni_tab"),
                        Repeater::make("Relatori / Tutor", "relatori_tutor")
                            ->layout("block")
                            ->collapsed("nome")
                            ->button("Aggiungi relatore")
                            ->fields([Text::make("Nome", "nome")]),
                        Taxonomy::make("Luogo", "luogo")
                            ->taxonomy("luogo-progetto")
                            ->appearance("multi_select")
                            ->create(true)
                            ->save(true),
                        WYSIWYGEditor::make("Beneficiari", "beneficiari")
                            ->toolbar([
                                "bold",
                                "italic",
                                "link",
                                "bullist",
                                "numlist",
                            ])
                            ->tabs("all")
                            ->disableMediaUpload(),
                        WYSIWYGEditor::make("Restituzione finale", "restituzione_finale")
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
                                        "primary"   => "Bottone primario",
                                        "secondary" => "Bottone secondario",
                                        "link"      => "Link",
                                    ])
                                    ->default("primary"),
                            ]),
                        Tab::make("Contenuti", "contenuti_tab"),
                        ...$tabs_fields,
                        Tab::make("Tassonomie", "tassonomie_tab"),
                        Taxonomy::make("Tipologia", "tipologia")
                            ->taxonomy("tipologia-progetto")
                            ->appearance("multi_select")
                            ->create(true)
                            ->save(true),
                        Taxonomy::make("Target", "target")
                            ->taxonomy("target-progetto")
                            ->appearance("multi_select")
                            ->create(true)
                            ->save(true),
                        Taxonomy::make("Status", "status")
                            ->taxonomy("status-progetto")
                            ->appearance("select")
                            ->create(true)
                            ->save(true),
                    ]),
            ],
        ]);

        register_extended_field_group([
            "title" => "Galleria",
            "location" => [Location::where("post_type", self::$names["slug"])],
            "hide_on_screen" => ["the_content"],
            "style" => "default",
            "position" => "normal",
            "fields" => [
                Group::make("Galleria", "galleria")
                    ->layout("block")
                    ->fields(
                        require get_stylesheet_directory() .
                            "/views/components/media-displayer/media-displayer.php",
                    ),
            ],
        ]);

        register_extended_field_group([
            "title" => "Partners",
            "location" => [Location::where("post_type", self::$names["slug"])],
            "hide_on_screen" => ["the_content"],
            "style" => "default",
            "position" => "normal",
            "fields" => [
                Group::make("Partners", "partners_displayer")
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
            "has_archive" => "progetti",
            "menu_icon" => "dashicons-portfolio",
            "menu_position" => null,
            "supports" => ["title", "thumbnail"],
            "labels" => [
                "name" => "Progetti",
                "singular_name" => "Progetto",
                "add_new" => "Aggiungi nuovo",
                "add_new_item" => "Aggiungi nuovo progetto",
                "edit_item" => "Modifica progetto",
                "new_item" => "Nuovo progetto",
                "view_item" => "Visualizza progetto",
                "search_items" => "Cerca progetti",
                "not_found" => "Nessun progetto trovato",
                "not_found_in_trash" => "Nessun progetto nel cestino",
                "all_items" => "Tutti i progetti",
            ],
        ];

        register_extended_post_type($name, $args, $names);
    }
}
