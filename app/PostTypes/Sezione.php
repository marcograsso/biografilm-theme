<?php

namespace App\PostTypes;

class Sezione extends \Timber\Post
{
    private static $names = [
        "singular" => "Sezione",
        "plural"   => "Sezioni",
        "slug"     => "sezione",
    ];

    public static function register(): void
    {
        self::register_post_type();

        add_filter("timber/post/classmap", function ($classmap) {
            return array_merge($classmap, [
                self::$names["slug"] => self::class,
            ]);
        });
    }

    private static function register_post_type(): void
    {
        $name  = self::$names["slug"];
        $names = self::$names;

        register_extended_post_type($name, [
            "menu_icon"    => "dashicons-category",
            "supports"     => ["title"],
            "public"       => false,
            "show_ui"      => true,
            "show_in_rest" => false,
            "labels"       => [
                "name"          => "Sezioni",
                "singular_name" => "Sezione",
                "add_new"       => "Aggiungi nuova",
                "add_new_item"  => "Aggiungi nuova sezione",
                "edit_item"     => "Modifica sezione",
                "new_item"      => "Nuova sezione",
                "search_items"  => "Cerca sezioni",
                "not_found"     => "Nessuna sezione trovata",
                "all_items"     => "Tutte le sezioni",
            ],
        ], $names);
    }
}
