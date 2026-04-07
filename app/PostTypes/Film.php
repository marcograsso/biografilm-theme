<?php

namespace App\PostTypes;

use Extended\ACF\Fields\Image;
use Extended\ACF\Fields\Number;
use Extended\ACF\Fields\Relationship;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Taxonomy;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\WYSIWYGEditor;
use Extended\ACF\Location;

class Film extends \Timber\Post
{
    private static $names = [
        "singular" => "Film",
        "plural" => "Film",
        "slug" => "film",
    ];

    public function get_proiezioni(): array
    {
        $items = get_field("proiezioni", $this->ID) ?: [];
        return array_map(fn($p) => new Proiezione(is_object($p) ? $p->ID : (int) $p), $items);
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

        add_action("admin_head-post.php", [
            self::class,
            "featured_image_helper",
        ]);
        add_action("admin_head-post-new.php", [
            self::class,
            "featured_image_helper",
        ]);
    }

    public static function featured_image_helper(): void
    {
        $screen = get_current_screen();
        if (!$screen || $screen->post_type !== self::$names["slug"]) {
            return;
        }
        echo '<style>#postimagediv .inside::after { content: "Questa immagine verrà utilizzata nelle card (formato 16:9) e in altri contesti orizzontali del sito. Si consiglia un\'immagine orizzontale ad alta risoluzione, preferibilmente 1920×1080px."; display: block; padding: 8px 12px; font-size: 12px; color: #757575; }</style>';
    }

    private static function register_custom_fields()
    {
        register_extended_field_group([
            "title" => "Film",
            "location" => [Location::where("post_type", self::$names["slug"])],
            "hide_on_screen" => ["the_content"],
            "style" => "",
            "fields" => [
                Tab::make("Generali"),
                Text::make("Regista", "regista"),
                Text::make("Titolo alternativo", "titolo_alternativo"),
                Text::make("Durata", "durata"),
                Number::make("Anno", "anno")
                    ->min(1888)
                    ->max(2100)
                    ->placeholder("es. 2024"),
                Text::make("Preview", "preview")->helperText(
                    "La preview viene automaticamente generata dall'unione di regista / durata / anno / genere. Se vuoi sovrascriverla, inserisci qui il testo manualmente. Es: Reid Davenport / 99' / 2023 / Doc. Altrimenti, lascia vuoto.",
                ),
                WYSIWYGEditor::make("Descrizione", "description")
                    ->toolbar(["bold", "italic", "link"])
                    ->tabs("all")
                    ->disableMediaUpload()
                    ->helperText(
                        "Questo testo esteso verrà mostrato nella pagina dedicata al film. Può essere anche lungo.",
                    ),

                Tab::make("Proiezioni", "proiezioni_tab"),
                Relationship::make("Proiezioni", "proiezioni")
                    ->key("field_film_proiezioni")
                    ->postTypes(["proiezione"])
                    ->filters(["search"])
                    ->elements(["featured_image"])
                    ->bidirectional("field_proiezione_film")
                    ->withSettings(["acfe_add_post" => 1]),

                Tab::make("Tassonomie"),
                Taxonomy::make("Sezione", "sezione")
                    ->taxonomy("sezione")
                    ->appearance("checkbox")
                    ->create(true)
                    ->save(true),
                Taxonomy::make("Paese", "paese")
                    ->taxonomy("paese")
                    ->appearance("multi_select")
                    ->create(true)
                    ->save(true),
                Taxonomy::make("Genere", "genere")
                    ->taxonomy("genere")
                    ->appearance("select")
                    ->create(true)
                    ->save(true),
                Taxonomy::make("Area tematica", "area_tematica")
                    ->taxonomy("area-tematica")
                    ->appearance("multi_select")
                    ->create(true)
                    ->save(true),
                Taxonomy::make("Badges", "badges")
                    ->taxonomy("badge")
                    ->appearance("multi_select")
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
            "menu_icon" => "dashicons-format-video",
            "menu_position" => null,
            "supports" => ["title", "thumbnail"],
            "labels" => [
                "name" => "Film",
                "singular_name" => "Film",
                "add_new" => "Aggiungi nuovo",
                "add_new_item" => "Aggiungi nuovo film",
                "edit_item" => "Modifica film",
                "new_item" => "Nuovo film",
                "view_item" => "Visualizza film",
                "search_items" => "Cerca film",
                "not_found" => "Nessun film trovato",
                "not_found_in_trash" => "Nessun film nel cestino",
                "all_items" => "Tutti i film",
            ],
        ];

        register_extended_post_type($name, $args, $names);
    }
}
