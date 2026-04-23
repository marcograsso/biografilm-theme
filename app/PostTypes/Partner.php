<?php

namespace App\PostTypes;

use Extended\ACF\ConditionalLogic;
use Extended\ACF\Fields\TrueFalse;
use Extended\ACF\Fields\URL;
use Extended\ACF\Location;

class Partner extends \Timber\Post
{
    private static $names = [
        "singular" => "Partner",
        "plural" => "Partners",
        "slug" => "partner",
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

        add_action("template_redirect", [self::class, "maybe_redirect"]);
        add_filter("wpseo_robots", [self::class, "maybe_noindex"]);
    }

    public static function maybe_redirect(): void
    {
        if (is_post_type_archive(self::$names["slug"])) {
            $partners_page = get_page_by_path("partners");
            $url = $partners_page
                ? get_permalink($partners_page)
                : home_url("/");
            wp_redirect($url, 301);
            exit();
        }

        if (!is_singular(self::$names["slug"])) {
            return;
        }

        $post_id = get_queried_object_id();

        if (!get_field("page_visible", $post_id)) {
            wp_redirect(home_url("/"));
            exit();
        }
    }

    public static function maybe_noindex(string $robots): string
    {
        if (!is_singular(self::$names["slug"])) {
            return $robots;
        }

        if (!get_field("page_visible", get_queried_object_id())) {
            return "noindex, nofollow";
        }

        return $robots;
    }

    private static function register_post_type()
    {
        $name = self::$names["slug"];
        $names = self::$names;
        $args = [
            "menu_icon" => "dashicons-groups",
            "menu_position" => null,
            "supports" => ["title", "editor", "thumbnail"],
            "labels" => [
                "name" => "Partners",
                "singular_name" => "Partner",
                "add_new" => "Aggiungi nuovo",
                "add_new_item" => "Aggiungi nuovo partner",
                "edit_item" => "Modifica partner",
                "new_item" => "Nuovo partner",
                "view_item" => "Visualizza partner",
                "search_items" => "Cerca partner",
                "not_found" => "Nessun partner trovato",
                "not_found_in_trash" => "Nessun partner nel cestino",
                "all_items" => "Tutti i partner",
            ],
        ];

        register_extended_post_type($name, $args, $names);
    }

    private static function register_custom_fields()
    {
        register_extended_field_group([
            "title" => "Visibilità Partner",
            "style" => "default",
            "location" => [Location::where("post_type", self::$names["slug"])],
            "fields" => [
                TrueFalse::make("Pagina visibile", "page_visible")
                    ->stylized()
                    ->default(false)
                    ->helperText(
                        "Se attiva, la pagina del partner è accessibile tramite URL diretto. Se disattivata, i visitatori vengono reindirizzati alla homepage e la pagina non viene indicizzata.",
                    ),
                URL::make("Link alternativo", "url")
                    ->helperText(
                        "Se la pagina non è visibile, questo link viene usato nella cella del displayer. Lascia vuoto per non avere nessun link.",
                    )
                    ->conditionalLogic([
                        ConditionalLogic::where("page_visible", "==", "0"),
                    ]),
            ],
        ]);
    }
}
