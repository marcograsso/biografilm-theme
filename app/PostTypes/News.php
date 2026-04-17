<?php

namespace App\PostTypes;

class News extends \Timber\Post
{
    private static $names = [
        "singular" => "News",
        "plural"   => "News",
        "slug"     => "news",
    ];

    public static function register()
    {
        self::register_post_type();
        self::register_taxonomy();

        add_filter("timber/post/classmap", function ($classmap) {
            return array_merge($classmap, [
                self::$names["slug"] => self::class,
            ]);
        });
    }

    private static function register_taxonomy(): void
    {
        register_extended_taxonomy(
            "news-category",
            ["news"],
            [
                "hierarchical" => true,
                "labels" => [
                    "name"          => "Categorie",
                    "singular_name" => "Categoria",
                    "add_new_item"  => "Aggiungi categoria",
                    "edit_item"     => "Modifica categoria",
                    "search_items"  => "Cerca categorie",
                    "not_found"     => "Nessuna categoria trovata",
                    "all_items"     => "Tutte le categorie",
                ],
            ],
            [
                "singular" => "Categoria",
                "plural"   => "Categorie",
                "slug"     => "news-category",
            ],
        );
    }

    private static function register_post_type()
    {
        $name  = self::$names["slug"];
        $names = self::$names;
        $args  = [
            "menu_icon"     => "dashicons-megaphone",
            "menu_position" => null,
            "supports"      => ["title", "editor", "thumbnail"],
            "labels"        => [
                "name"               => "News",
                "singular_name"      => "News",
                "add_new"            => "Aggiungi nuova",
                "add_new_item"       => "Aggiungi nuova news",
                "edit_item"          => "Modifica news",
                "new_item"           => "Nuova news",
                "view_item"          => "Visualizza news",
                "search_items"       => "Cerca news",
                "not_found"          => "Nessuna news trovata",
                "not_found_in_trash" => "Nessuna news nel cestino",
                "all_items"          => "Tutte le news",
            ],
        ];

        register_extended_post_type($name, $args, $names);
    }
}
