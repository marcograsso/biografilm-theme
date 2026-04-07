<?php

namespace App\PostTypes;

use Extended\ACF\Fields\DatePicker;
use Extended\ACF\Fields\Image;
use Extended\ACF\Fields\Link;
use Extended\ACF\Fields\Relationship;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Taxonomy;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\TimePicker;
use Extended\ACF\Fields\URL;
use Extended\ACF\Fields\WYSIWYGEditor;
use Extended\ACF\Location;

class Proiezione extends \Timber\Post
{
    private static $names = [
        "singular" => "Proiezione",
        "plural" => "Proiezioni",
        "slug" => "proiezione",
    ];

    public function get_film(): ?Film
    {
        $films = get_field("film", $this->ID);
        if (empty($films)) {
            return null;
        }
        $film = is_array($films) ? $films[0] : $films;
        $id = is_object($film) ? $film->ID : (int) $film;
        return new Film($id);
    }

    public static function register()
    {
        self::register_post_type();

        add_filter("timber/post/classmap", function ($classmap) {
            return array_merge($classmap, [
                self::$names["slug"] => self::class,
            ]);
        });

        self::register_custom_fields();

        // Proiezioni have no public single pages
        add_action("template_redirect", function () {
            if (is_singular(self::$names["slug"])) {
                wp_redirect(home_url(), 301);
                exit;
            }
        });
    }

    private static function register_custom_fields()
    {
        register_extended_field_group([
            "title" => "Proiezione",
            "location" => [Location::where("post_type", self::$names["slug"])],
            "hide_on_screen" => ["the_content"],
            "style" => "",
            "fields" => [
                Tab::make("Proiezione"),
                Relationship::make("Film", "film")
                    ->key("field_proiezione_film")
                    ->postTypes(["film"])
                    ->filters(["search"])
                    ->elements(["featured_image"])
                    ->maxPosts(1)
                    ->bidirectional("field_film_proiezioni"),
                DatePicker::make("Data", "data")
                    ->displayFormat("d/m/Y")
                    ->format("d F Y"),
                TimePicker::make("Orario", "orario")
                    ->displayFormat("H:i")
                    ->format("H:i"),
                Taxonomy::make("Location", "location")
                    ->taxonomy("location")
                    ->appearance("multi_select")
                    ->create(true)
                    ->save(true),
                WYSIWYGEditor::make(
                    "Descrizione proiezione",
                    "descrizione_proiezione",
                )
                    ->toolbar(["bold", "italic", "link"])
                    ->tabs("all")
                    ->disableMediaUpload(),

                Tab::make("Biglietti", "biglietti_tab"),
                Link::make("Biglietti", "biglietti")->format("array"),
                URL::make("Link biglietto", "link_biglietto"),
                URL::make("Link MyMovies", "link_mymovies"),
                Link::make(
                    "Link personalizzabile",
                    "link_personalizzabile",
                )->format("array"),

                Tab::make("Info alternative"),
                Text::make("Titolo alternativo", "titolo_alternativo"),
                Text::make("Preview alternativa", "preview_alternativa"),
                Image::make(
                    "Immagine alternativa",
                    "immagine_alternativa",
                )->format("array"),
                Taxonomy::make("Alt Badges", "alt_badges")
                    ->taxonomy("badge")
                    ->appearance("checkbox")
                    ->create(true)
                    ->save(true),
            ],
        ]);
    }

    private static function register_post_type()
    {
        $name = self::$names["slug"];
        $names = self::$names;
        $args = [
            "menu_icon" => "dashicons-tickets-alt",
            "menu_position" => null,
            "supports" => ["title", "thumbnail"],
            "labels" => [
                "name" => "Proiezioni",
                "singular_name" => "Proiezione",
                "add_new" => "Aggiungi nuova",
                "add_new_item" => "Aggiungi nuova proiezione",
                "edit_item" => "Modifica proiezione",
                "new_item" => "Nuova proiezione",
                "view_item" => "Visualizza proiezione",
                "search_items" => "Cerca proiezioni",
                "not_found" => "Nessuna proiezione trovata",
                "not_found_in_trash" => "Nessuna proiezione nel cestino",
                "all_items" => "Tutte le proiezioni",
            ],
        ];

        register_extended_post_type($name, $args, $names);
    }
}
