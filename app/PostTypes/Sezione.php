<?php

namespace App\PostTypes;

use Extended\ACF\Location;
use Timber\Timber;

class Sezione extends \Timber\Post
{
    private static $names = [
        "singular" => "Sezione",
        "plural"   => "Sezioni",
        "slug"     => "sezione",
    ];

    public function get_films(): array
    {
        $posts = get_posts([
            "post_type"      => "film",
            "post_status"    => "publish",
            "posts_per_page" => -1,
            "orderby"        => "title",
            "order"          => "ASC",
            "meta_query"     => [[
                "key"     => "sezione",
                "value"   => '"' . $this->ID . '"',
                "compare" => "LIKE",
            ]],
        ]);

        return array_map(fn($p) => Timber::get_post($p->ID), $posts);
    }

    public static function register(): void
    {
        self::register_post_type();

        add_filter("timber/post/classmap", function ($classmap) {
            return array_merge($classmap, [
                self::$names["slug"] => self::class,
            ]);
        });

        self::register_custom_fields();
    }

    private static function register_custom_fields(): void
    {
        $text_fields = require get_stylesheet_directory() . "/views/components/text-displayer/text-displayer.php";

        register_extended_field_group([
            "title"          => "Contenuto",
            "location"       => [Location::where("post_type", self::$names["slug"])],
            "hide_on_screen" => ["the_content"],
            "style"          => "default",
            "fields"         => $text_fields,
        ]);
    }

    private static function register_post_type(): void
    {
        $name  = self::$names["slug"];
        $names = self::$names;

        register_extended_post_type($name, [
            "menu_icon"          => "dashicons-category",
            "supports"           => ["title", "thumbnail"],
            "public"             => true,
            "publicly_queryable" => true,
            "show_ui"            => true,
            "show_in_rest"       => true,
            "has_archive"        => "sezioni",
            "rewrite"            => ["slug" => "sezione", "with_front" => false],
            "labels"             => [
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
