<?php

namespace App\PostTypes;

class Ospitalita extends \Timber\Post
{
    private static $names = [
        "singular" => "Ospitalità",
        "plural"   => "Ospitalità",
        "slug"     => "ospitalita",
    ];

    public static function register()
    {
        self::register_post_type();

        add_filter("timber/post/classmap", function ($classmap) {
            return array_merge($classmap, [
                self::$names["slug"] => self::class,
            ]);
        });

        add_action("template_redirect", [self::class, "maybe_redirect"]);
    }

    public static function maybe_redirect(): void
    {
        if (!is_post_type_archive(self::$names["slug"])) {
            return;
        }

        $page = get_page_by_path("ospitality");
        $url = $page ? get_permalink($page) : home_url("/");
        wp_redirect($url, 301);
        exit();
    }

    private static function register_post_type()
    {
        $name  = self::$names["slug"];
        $names = self::$names;
        $args  = [
            "menu_icon"     => "dashicons-building",
            "menu_position" => null,
            "supports"      => ["title", "editor", "thumbnail"],
            "labels"        => [
                "name"               => "Ospitalità",
                "singular_name"      => "Ospitalità",
                "add_new"            => "Aggiungi nuova",
                "add_new_item"       => "Aggiungi nuova ospitalità",
                "edit_item"          => "Modifica ospitalità",
                "new_item"           => "Nuova ospitalità",
                "view_item"          => "Visualizza ospitalità",
                "search_items"       => "Cerca ospitalità",
                "not_found"          => "Nessuna ospitalità trovata",
                "not_found_in_trash" => "Nessuna ospitalità nel cestino",
                "all_items"          => "Tutte le ospitalità",
            ],
        ];

        register_extended_post_type($name, $args, $names);
    }
}
