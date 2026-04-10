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

    public static function sync_film_taxonomies(int $post_id): void
    {
        $films = get_field("film", $post_id);
        if (empty($films)) {
            return;
        }

        $film_id = is_array($films) ? $films[0]->ID : $films->ID;

        foreach (["paese", "genere", "area-tematica", "badge"] as $taxonomy) {
            $term_ids = wp_get_post_terms($film_id, $taxonomy, ["fields" => "ids"]);
            wp_set_post_terms($post_id, is_wp_error($term_ids) ? [] : $term_ids, $taxonomy);
        }

        // Sync sezione relationship from film
        $sezione = get_field("sezione", $film_id);
        $sezione_ids = !empty($sezione)
            ? array_map(fn($p) => is_object($p) ? $p->ID : (int) $p, (array) $sezione)
            : [];
        update_field("field_proiezione_sezione", $sezione_ids, $post_id);

        update_post_meta($post_id, '_film_title', get_the_title($film_id));
        update_post_meta($post_id, '_film_regista', get_post_meta($film_id, 'regista', true));
        update_post_meta($post_id, '_film_sezione', !empty($sezione_ids) ? get_the_title($sezione_ids[0]) : '');
    }

    public static function search_join(string $join, \WP_Query $query): string
    {
        global $wpdb;
        if (empty($query->get('s'))) {
            return $join;
        }
        $join .= " LEFT JOIN {$wpdb->postmeta} AS pm_film_title ON ({$wpdb->posts}.ID = pm_film_title.post_id AND pm_film_title.meta_key = '_film_title')";
        $join .= " LEFT JOIN {$wpdb->postmeta} AS pm_film_regista ON ({$wpdb->posts}.ID = pm_film_regista.post_id AND pm_film_regista.meta_key = '_film_regista')";
        return $join;
    }

    public static function search_where(string $search, \WP_Query $query): string
    {
        global $wpdb;
        if (empty($query->get('s')) || empty($search)) {
            return $search;
        }
        $like = '%' . $wpdb->esc_like($query->get('s')) . '%';
        $extra = $wpdb->prepare('OR pm_film_title.meta_value LIKE %s OR pm_film_regista.meta_value LIKE %s', $like, $like);
        // Insert before the last closing paren of the search clause
        $pos = strrpos($search, ')');
        if ($pos !== false) {
            $search = substr_replace($search, ' ' . $extra, $pos, 0);
        }
        return $search;
    }

    public static function search_distinct(string $distinct, \WP_Query $query): string
    {
        if (!empty($query->get('s'))) {
            return 'DISTINCT';
        }
        return $distinct;
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

        add_action("save_post_proiezione", [self::class, "sync_film_taxonomies"]);

        add_filter('posts_join', [self::class, 'search_join'], 10, 2);
        add_filter('posts_search', [self::class, 'search_where'], 10, 2);
        add_filter('posts_distinct', [self::class, 'search_distinct'], 10, 2);

        add_action("admin_menu", function () {
            foreach (["paese", "genere", "area-tematica", "badge"] as $taxonomy) {
                remove_meta_box("tagsdiv-{$taxonomy}", "proiezione", "side");
            }
        });

        // Proiezioni have no public single pages
        add_action("template_redirect", function () {
            if (is_singular(self::$names["slug"])) {
                wp_redirect(home_url(), 301);
                exit();
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
                    ->format("Ymd"),
                TimePicker::make("Orario", "orario")
                    ->displayFormat("H:i")
                    ->format("H:i"),
                Taxonomy::make("Location", "location")
                    ->taxonomy("location")
                    ->appearance("multi_select")
                    ->create(true)
                    ->save(true),
                Text::make("Sala o altre informazioni location", "sala_location")
                    ->helperText("Informazioni aggiuntive sulla location, es. «Sala 1», «Arena esterna», ecc."),
                WYSIWYGEditor::make(
                    "Descrizione proiezione",
                    "descrizione_proiezione",
                )
                    ->toolbar(["bold", "italic", "link"])
                    ->tabs("all")
                    ->disableMediaUpload(),

                Tab::make("Biglietti", "biglietti_tab"),

                Link::make("Link biglietto", "link_biglietto")
                    ->helperText("Link principale per l'acquisto del biglietto. Viene mostrato come pulsante primario. Titolo predefinito consigliato: «Compra biglietto»."),
                Link::make(
                    "Link extra personalizzabile",
                    "link_personalizzabile",
                )->format("array")
                    ->helperText("Link secondario con testo personalizzabile. Utile per biglietterie alternative o pagine di informazioni aggiuntive."),
                Link::make("Link MyMovies", "link_mymovies")
                    ->helperText("Link diretto alla pagina MyMovies. Titolo predefinito consigliato: «Guarda online su MyMovies»."),

                Tab::make("Sezione"),
                Relationship::make("Sezione", "sezione")
                    ->key("field_proiezione_sezione")
                    ->postTypes(["sezione"])
                    ->filters(["search"])
                    ->maxPosts(1)
                    ->withSettings(["readonly" => 1])
                    ->helperText("Sincronizzata automaticamente dal film associato."),

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
