<?php

namespace App\PostTypes;

use Extended\ACF\Fields\Group;
use Extended\ACF\Fields\Link;
use Extended\ACF\Fields\Repeater;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Taxonomy;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\Textarea;
use Extended\ACF\Fields\WYSIWYGEditor;
use Extended\ACF\Location;

class WhosComing extends \Timber\Post
{
    private static $names = [
        "singular" => "Who's Coming",
        "plural"   => "Who's Coming",
        "slug"     => "whos-coming",
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

    private static function register_post_type()
    {
        $name  = self::$names["slug"];
        $names = self::$names;
        $args  = [
            "menu_icon"     => "dashicons-id",
            "menu_position" => null,
            "supports"      => ["title", "thumbnail"],
            "rewrite"       => ["slug" => "industry/whos-coming"],
            "labels"        => [
                "name"               => "Who's Coming",
                "singular_name"      => "Who's Coming",
                "add_new"            => "Aggiungi nuovo",
                "add_new_item"       => "Aggiungi nuovo partecipante",
                "edit_item"          => "Modifica partecipante",
                "new_item"           => "Nuovo partecipante",
                "view_item"          => "Visualizza partecipante",
                "search_items"       => "Cerca partecipanti",
                "not_found"          => "Nessun partecipante trovato",
                "not_found_in_trash" => "Nessun partecipante nel cestino",
                "all_items"          => "Tutti i partecipanti",
            ],
        ];

        register_extended_post_type($name, $args, $names);
    }

    private static function register_custom_fields()
    {
        register_extended_field_group([
            "title"          => "Who's Coming",
            "location"       => [Location::where("post_type", self::$names["slug"])],
            "hide_on_screen" => ["the_content"],
            "style"          => "default",
            "position"       => "normal",
            "fields"         => [
                Group::make("Who's Coming", "whos_coming")
                    ->layout("block")
                    ->fields([
                        Tab::make("Informazioni", "informazioni_tab"),
                        Text::make("Professione libera", "professione_libera")
                            ->helperText("Campo testuale libero, in alternativa alla tassonomia."),
                        Group::make("Azienda", "azienda")
                            ->layout("block")
                            ->fields([
                                Text::make("Nome azienda", "titolo"),
                                Textarea::make("Descrizione azienda", "descrizione")
                                    ->rows(3),
                            ]),

                        Tab::make("Testo", "testo_tab"),
                        WYSIWYGEditor::make("Testo", "testo")
                            ->toolbar(["bold", "italic", "link", "bullist", "numlist"])
                            ->tabs("all")
                            ->disableMediaUpload(),

                        Tab::make("Link utili", "links_tab"),
                        Repeater::make("Link utili", "link_utili")
                            ->layout("block")
                            ->collapsed("link")
                            ->button("Aggiungi link")
                            ->fields([
                                Link::make("Link", "link")->format("array"),
                            ]),

                        Tab::make("Tassonomie", "tassonomie_tab"),
                        Taxonomy::make("Accredito", "accredito")
                            ->taxonomy("accredito-whos-coming")
                            ->appearance("multi_select")
                            ->create(true)
                            ->save(true),
                        Taxonomy::make("Professione", "professione")
                            ->taxonomy("professione-whos-coming")
                            ->appearance("multi_select")
                            ->create(true)
                            ->save(true),
                        Taxonomy::make("Paese", "paese")
                            ->taxonomy("paese-whos-coming")
                            ->appearance("select")
                            ->create(true)
                            ->save(true),
                    ]),
            ],
        ]);
    }
}
