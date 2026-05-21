<?php

namespace App\PostTypes;

use Extended\ACF\Fields\FlexibleContent;
use Extended\ACF\Fields\Group;
use Extended\ACF\Fields\Image;
use Extended\ACF\Fields\Layout;
use Extended\ACF\Fields\Link;
use Extended\ACF\Fields\PostObject;
use Extended\ACF\Fields\Relationship;
use Extended\ACF\Fields\Repeater;
use Extended\ACF\Fields\Select;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\Textarea;
use Extended\ACF\Fields\WYSIWYGEditor;
use Extended\ACF\Location;

class ContentsDoc extends \Timber\Post
{
    private static $names = [
        "singular" => "Content Doc",
        "plural" => "Contents Doc",
        "slug" => "contents-doc",
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

    private static function register_custom_fields()
    {
        $tabs_fields = require get_stylesheet_directory() .
            "/views/components/tabs/tabs.php";

        $text_displayer_fields = require get_stylesheet_directory() .
            "/views/components/text-displayer/text-displayer.php";

        $editorial_block_fields = require get_stylesheet_directory() .
            "/views/components/editorial-block/editorial-block.php";

        $spacer_fields = require get_stylesheet_directory() .
            "/views/components/spacer/spacer.php";

        $logos_displayer_fields = require get_stylesheet_directory() .
            "/views/components/logos-displayer/logos-displayer.php";

        register_extended_field_group([
            "title" => "Scheda Doc",
            "location" => [Location::where("post_type", self::$names["slug"])],
            "hide_on_screen" => ["the_content"],
            "style" => "default",
            "position" => "normal",
            "fields" => [
                Group::make("", "content_doc")
                    ->layout("block")
                    ->fields([
                        Tab::make("Generali", "generali_tab"),
                        Image::make("Icona", "icon")->format("array"),
                        Text::make("Sottotitolo", "sottotitolo"),
                        Textarea::make("Descrizione breve", "descrizione_breve")
                            ->rows(4)
                            ->helperText(
                                "Testo di anteprima usato nelle card.",
                            ),
                        Tab::make("Informazioni utili", "informazioni_tab"),
                        Repeater::make("Informazioni utili", "informazioni")
                            ->layout("block")
                            ->collapsed("label")
                            ->button("Aggiungi voce")
                            ->fields([
                                Text::make("Etichetta", "label"),
                                WYSIWYGEditor::make("Valore", "value")
                                    ->toolbar(["bold", "italic", "link"])
                                    ->tabs("all")
                                    ->disableMediaUpload()
                                    ->withSettings([
                                        "acfe_wysiwyg_height" => 60,
                                    ]),
                            ]),

                        Tab::make("Links", "links_tab"),
                        Repeater::make("Links", "links")
                            ->layout("block")
                            ->collapsed("link")
                            ->button("Aggiungi link")
                            ->fields([
                                Link::make("Link", "link")->format("array"),
                                Select::make("Stile", "stile")
                                    ->choices([
                                        "primary" => "Bottone primario",
                                        "secondary" => "Bottone secondario",
                                        "link" => "Link",
                                    ])
                                    ->default("primary"),
                            ]),

                        Tab::make("Tabs", "contenuti_tab"),
                        ...$tabs_fields,

                        Tab::make("Extra", "extra_tab"),
                        FlexibleContent::make("Extra", "extra")
                            ->key("field_contents_doc_extra")
                            ->button("Aggiungi sezione")
                            ->layouts([
                                Layout::make("Progetti", "progetti")
                                    ->key("layout_contents_doc_extra_progetti")
                                    ->fields([
                                        Text::make("Titolo", "titolo")
                                            ->key("field_contents_doc_extra_progetti_titolo"),
                                        Text::make("Sottotitolo", "sottotitolo")
                                            ->key("field_contents_doc_extra_progetti_sottotitolo"),
                                        Relationship::make("Progetti", "items")
                                            ->key("field_contents_doc_extra_progetti_items")
                                            ->postTypes(["progetti-doc"])
                                            ->filters(["search"])
                                            ->elements(["featured_image"]),
                                    ]),
                                Layout::make("Who's Coming", "whos_coming")
                                    ->key("layout_contents_doc_extra_whos_coming")
                                    ->fields([
                                        Text::make("Titolo", "titolo")
                                            ->key("field_contents_doc_extra_whos_coming_titolo"),
                                        Text::make("Sottotitolo", "sottotitolo")
                                            ->key("field_contents_doc_extra_whos_coming_sottotitolo"),
                                        Relationship::make("Who's Coming", "items")
                                            ->key("field_contents_doc_extra_whos_coming_items")
                                            ->postTypes(["whos-coming"])
                                            ->filters(["search"])
                                            ->elements(["featured_image"]),
                                    ]),
                                Layout::make("Selection", "selection")
                                    ->key("layout_contents_doc_extra_selection")
                                    ->fields([
                                        Text::make("Titolo", "titolo")
                                            ->key("field_contents_doc_extra_selection_titolo"),
                                        Text::make("Sottotitolo", "sottotitolo")
                                            ->key("field_contents_doc_extra_selection_sottotitolo"),
                                        Repeater::make("Selezioni", "items")
                                            ->key("field_contents_doc_extra_selection_items")
                                            ->layout("block")
                                            ->button("Aggiungi selezione")
                                            ->fields([
                                                Tab::make("Sinistra", "sinistra_tab"),
                                                Image::make("Immagine", "immagine")
                                                    ->key("field_contents_doc_extra_selection_item_immagine")
                                                    ->format("array"),
                                                Text::make("Titolo colonna", "titolo_colonna")
                                                    ->key("field_contents_doc_extra_selection_item_titolo_colonna"),
                                                WYSIWYGEditor::make("Descrizione", "descrizione")
                                                    ->key("field_contents_doc_extra_selection_item_descrizione")
                                                    ->toolbar(["bold", "italic", "link"])
                                                    ->tabs("all")
                                                    ->disableMediaUpload(),
                                                Tab::make("Destra", "destra_tab"),
                                                PostObject::make("Progetto", "progetto")
                                                    ->key("field_contents_doc_extra_selection_item_progetto")
                                                    ->postTypes(["progetti-doc"]),
                                            ]),
                                    ]),
                                Layout::make("Attività", "attivita")
                                    ->key("layout_contents_doc_extra_attivita")
                                    ->fields([
                                        Text::make("Titolo", "titolo")
                                            ->key("field_contents_doc_extra_attivita_titolo"),
                                        Text::make("Sottotitolo", "sottotitolo")
                                            ->key("field_contents_doc_extra_attivita_sottotitolo"),
                                        Relationship::make("Attività", "items")
                                            ->key("field_contents_doc_extra_attivita_items")
                                            ->postTypes(["attivita-doc", "attivita-drama"])
                                            ->filters(["search"])
                                            ->elements(["featured_image"]),
                                    ]),
                                Layout::make("Text Displayer", "text_displayer")
                                    ->key("layout_contents_doc_extra_text_displayer")
                                    ->fields($text_displayer_fields),
                                Layout::make("Editorial Block", "editorial_block")
                                    ->key("layout_contents_doc_extra_editorial_block")
                                    ->fields($editorial_block_fields),
                                Layout::make("Spacer", "spacer")
                                    ->key("layout_contents_doc_extra_spacer")
                                    ->fields($spacer_fields),
                                Layout::make("Logos Displayer", "logos_displayer")
                                    ->key("layout_contents_doc_extra_logos_displayer")
                                    ->fields($logos_displayer_fields),
                                Layout::make("Premi", "premi")
                                    ->key("layout_contents_doc_extra_premi")
                                    ->fields([
                                        Text::make("Titolo", "titolo")
                                            ->key("field_contents_doc_extra_premi_titolo"),
                                        Text::make("Sottotitolo", "sottotitolo")
                                            ->key("field_contents_doc_extra_premi_sottotitolo"),
                                        Repeater::make("Premi", "items")
                                            ->key("field_contents_doc_extra_premi_items")
                                            ->layout("block")
                                            ->button("Aggiungi premio")
                                            ->fields([
                                                Text::make("Titolo", "titolo")
                                                    ->key("field_contents_doc_extra_premi_item_titolo"),
                                                Link::make("Powered by", "powered_by")
                                                    ->key("field_contents_doc_extra_premi_item_powered_by")
                                                    ->format("array"),
                                                WYSIWYGEditor::make("Contenuto", "contenuto")
                                                    ->key("field_contents_doc_extra_premi_item_contenuto")
                                                    ->toolbar(["bold", "italic", "link", "bullist", "numlist"])
                                                    ->tabs("all")
                                                    ->disableMediaUpload(),
                                            ]),
                                    ]),
                            ]),
                    ]),
            ],
        ]);
    }

    private static function register_post_type()
    {
        $name = self::$names["slug"];
        $names = self::$names;
        $args = [
            "menu_icon" => "dashicons-video-alt2",
            "menu_position" => null,
            "rewrite" => ["slug" => "industry/bio-to-b-doc/contents"],
            "supports" => ["title", "editor", "thumbnail"],
            "labels" => [
                "name" => "Contents Doc",
                "singular_name" => "Content Doc",
                "add_new" => "Aggiungi nuovo",
                "add_new_item" => "Aggiungi nuovo content doc",
                "edit_item" => "Modifica content doc",
                "new_item" => "Nuovo content doc",
                "view_item" => "Visualizza content doc",
                "search_items" => "Cerca contents doc",
                "not_found" => "Nessun content doc trovato",
                "not_found_in_trash" => "Nessun content doc nel cestino",
                "all_items" => "Tutti i contents doc",
            ],
        ];

        register_extended_post_type($name, $args, $names);
    }
}
