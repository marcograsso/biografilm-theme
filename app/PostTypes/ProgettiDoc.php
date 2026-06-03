<?php

namespace App\PostTypes;

use Extended\ACF\Fields\Number;
use Extended\ACF\Fields\PostObject;
use Extended\ACF\Fields\Relationship;
use Extended\ACF\Fields\Repeater;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Taxonomy;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\WYSIWYGEditor;
use Extended\ACF\Location;

class ProgettiDoc extends \Timber\Post
{
    private static $names = [
        "singular" => "Progetto Doc",
        "plural" => "Progetti Doc",
        "slug" => "progetti-doc",
    ];

    public static function register()
    {
        self::register_post_type();

        add_filter("timber/post/classmap", function ($classmap) {
            return array_merge($classmap, [
                self::$names["slug"] => self::class,
            ]);
        });

        self::register_custom_fields();

        add_action("template_redirect", function () {
            $path = rtrim(
                parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH),
                "/",
            );
            if ($path === "/industry/bio-to-b-doc/progetti") {
                wp_redirect(get_post_type_archive_link("contents-doc"), 301);
                exit();
            }
        });

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
            "title" => "Progetto Doc",
            "location" => [Location::where("post_type", self::$names["slug"])],
            "hide_on_screen" => ["the_content"],
            "style" => "",
            "fields" => [
                Tab::make("Generali"),
                PostObject::make("Sezione (Contents Doc)", "contents_doc_parent")
                    ->postTypes(["contents-doc"])
                    ->nullable(),
                Text::make("Regista", "regista"),
                Repeater::make("Altri registi", "altri_registi")
                    ->layout("row")
                    ->key("field_progetti_doc_altri_registi")
                    ->collapsed("field_progetti_doc_altri_registi_nome")
                    ->fields([
                        Text::make("Nome", "nome")->key(
                            "field_progetti_doc_altri_registi_nome",
                        ),
                    ]),
                Text::make(
                    "Titolo aggiuntivo / Additional title",
                    "titolo_alternativo",
                ),
                Repeater::make(
                    "Altri titoli alternativi",
                    "altri_titoli_alternativi",
                )
                    ->layout("row")
                    ->key("field_progetti_doc_altri_titoli_alternativi")
                    ->collapsed(
                        "field_progetti_doc_altri_titoli_alternativi_titolo",
                    )
                    ->fields([
                        Text::make("Titolo", "titolo")->key(
                            "field_progetti_doc_altri_titoli_alternativi_titolo",
                        ),
                    ]),
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
                        "Questo testo esteso verrà mostrato nella pagina dedicata al progetto. Può essere anche lungo.",
                    ),

                Tab::make("Tassonomie"),
                Taxonomy::make("Paese", "paese")
                    ->taxonomy("pd-paese")
                    ->appearance("multi_select")
                    ->create(true)
                    ->save(true),
                Taxonomy::make("Formato / Format", "genere")
                    ->taxonomy("pd-genere")
                    ->appearance("multi_select")
                    ->create(true)
                    ->save(true),
                Taxonomy::make("Badges", "badges")
                    ->taxonomy("pd-badge")
                    ->appearance("multi_select")
                    ->create(true)
                    ->save(true),

                Tab::make("Info aggiuntive"),
                Repeater::make("Info aggiuntive", "info_aggiuntive")
                    ->key("field_progetti_doc_info_aggiuntive_repeater")
                    ->helperText(
                        "Le righe aggiunte qui appariranno nella tabella informativa della pagina del progetto, dopo i campi standard (regista, durata, paese, ecc.).",
                    )
                    ->fields([
                        Text::make("Titolo", "titolo")->key(
                            "field_progetti_doc_info_aggiuntive_titolo",
                        ),
                        WYSIWYGEditor::make("Contenuto", "contenuto")
                            ->key(
                                "field_progetti_doc_info_aggiuntive_contenuto",
                            )
                            ->toolbar(["bold", "italic", "link"])
                            ->tabs("all")
                            ->disableMediaUpload(),
                    ]),

                Tab::make("Who's Coming", "whos_coming_tab"),
                Repeater::make("Who's Coming", "whos_coming")
                    ->key("field_progetti_doc_whos_coming")
                    ->fields([
                        Text::make("Titolo", "titolo")->key(
                            "field_progetti_doc_whos_coming_titolo",
                        ),
                        Relationship::make("Profili", "profili")
                            ->key("field_progetti_doc_whos_coming_profili")
                            ->postTypes(["whos-coming"])
                            ->filters(["search"])
                            ->elements(["featured_image"]),
                    ]),
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
            "has_archive" => false,
            "rewrite" => ["slug" => "industry/bio-to-b-doc/progetti"],
            "supports" => ["title", "thumbnail"],
            "labels" => [
                "name" => "Progetti Doc",
                "singular_name" => "Progetto Doc",
                "add_new" => "Aggiungi nuovo",
                "add_new_item" => "Aggiungi nuovo progetto doc",
                "edit_item" => "Modifica progetto doc",
                "new_item" => "Nuovo progetto doc",
                "view_item" => "Visualizza progetto doc",
                "search_items" => "Cerca progetti doc",
                "not_found" => "Nessun progetto doc trovato",
                "not_found_in_trash" => "Nessun progetto doc nel cestino",
                "all_items" => "Tutti i progetti doc",
            ],
        ];

        register_extended_post_type($name, $args, $names);
    }
}
