<?php

namespace App\PostTypes;

use Extended\ACF\Fields\Group;
use Extended\ACF\Fields\Image;
use Extended\ACF\Fields\Link;
use Extended\ACF\Fields\Repeater;
use Extended\ACF\Fields\Select;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\Textarea;
use Extended\ACF\Fields\WYSIWYGEditor;
use Extended\ACF\Location;

class ContentsDoc extends \Timber\Post
{
    private static $names = [
        "singular" => "Content Doc",
        "plural" => "Contents Doc",
        "slug" => "contents-doc",
    ];

    public static function register()
    {
        self::register_post_type();
        self::register_custom_fields();

        add_filter("timber/post/classmap", function ($classmap) {
            return array_merge($classmap, [
                self::$names["slug"] => self::class,
            ]);
        });
    }

    private static function register_custom_fields()
    {
        $tabs_fields = require get_stylesheet_directory() .
            "/views/components/tabs/tabs.php";

        register_extended_field_group([
            "title" => "Content Doc",
            "location" => [Location::where("post_type", self::$names["slug"])],
            "hide_on_screen" => ["the_content"],
            "style" => "default",
            "position" => "normal",
            "fields" => [
                Group::make("Content Doc", "content_doc")
                    ->layout("block")
                    ->fields([
                        Tab::make("Generali", "generali_tab"),
                        Image::make("Icona", "icon")->format("array"),
                        Text::make("Sottotitolo", "sottotitolo"),
                        Textarea::make("Descrizione breve", "descrizione_breve")
                            ->rows(4)
                            ->helperText(
                                "Testo di anteprima usato nelle card.",
                            ),
                        Tab::make("Informazioni utili", "informazioni_tab"),
                        Repeater::make("Informazioni utili", "informazioni")
                            ->layout("block")
                            ->collapsed("label")
                            ->button("Aggiungi voce")
                            ->fields([
                                Text::make("Etichetta", "label"),
                                WYSIWYGEditor::make("Valore", "value")
                                    ->toolbar(["bold", "italic", "link"])
                                    ->tabs("all")
                                    ->disableMediaUpload()
                                    ->withSettings([
                                        "acfe_wysiwyg_height" => 60,
                                    ]),
                            ]),

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

                        Tab::make("Tabs", "contenuti_tab"),
                        ...$tabs_fields,
                    ]),
            ],
        ]);
    }

    private static function register_post_type()
    {
        $name = self::$names["slug"];
        $names = self::$names;
        $args = [
            "menu_icon" => "dashicons-video-alt2",
            "menu_position" => null,
            "rewrite" => ["slug" => "industry/bio-to-bdoc/contents"],
            "supports" => ["title", "editor", "thumbnail"],
            "labels" => [
                "name" => "Contents Doc",
                "singular_name" => "Content Doc",
                "add_new" => "Aggiungi nuovo",
                "add_new_item" => "Aggiungi nuovo content doc",
                "edit_item" => "Modifica content doc",
                "new_item" => "Nuovo content doc",
                "view_item" => "Visualizza content doc",
                "search_items" => "Cerca contents doc",
                "not_found" => "Nessun content doc trovato",
                "not_found_in_trash" => "Nessun content doc nel cestino",
                "all_items" => "Tutti i contents doc",
            ],
        ];

        register_extended_post_type($name, $args, $names);
    }
}
