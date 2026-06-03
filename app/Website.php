<?php

namespace App;

use Timber\Site;
use Timber\Timber;
use Timber\URLHelper;
use localghost\Twig\Extra\Hateml\HatemlExtension;
use TalesFromADev\Twig\Extra\Tailwind\TailwindExtension;
use TalesFromADev\Twig\Extra\Tailwind\TailwindRuntime;

use App\Vite;
use Yard\Hook\Action;
use Yard\Hook\Filter;

class Website extends Site
{
    public function __construct()
    {
        $this->vite = new Vite();
        parent::__construct();
    }

    #[Action("init")]
    public function register_post_types()
    {
        PostTypes\Sezione::register();
        PostTypes\Film::register();
        PostTypes\Proiezione::register();
        PostTypes\News::register();
        PostTypes\Partner::register();
        PostTypes\Ospitalita::register();
        PostTypes\Progetti::register();
        PostTypes\Eventi::register();
        PostTypes\WhosComing::register();
        PostTypes\ContentsDoc::register();
        PostTypes\ContentsDrama::register();
        PostTypes\ProgettiDoc::register();
        PostTypes\ProgettiDrama::register();
        PostTypes\AttivitaDoc::register();
        PostTypes\AttivitaDrama::register();
        PostTypes\Producers::register();
        PostTypes\Publishers::register();
        PostTypes\ProposteEditoriali::register();
        PostTypes\EventiProgramma::register();
    }

    #[Action("init")]
    public function register_page_rewrite_rules()
    {
        // Deep page URLs intercepted by CPT rewrite rules need top-priority rules
        $pages = [
            "industry/bio-to-b-doc/programma-doc",
            "industry/bio-to-b-drama/programma-drama",
        ];
        foreach ($pages as $path) {
            add_rewrite_rule(
                "^" . $path . '/?$',
                "index.php?pagename=" . $path,
                "top",
            );
        }
    }

    #[Action("init")]
    public function register_taxonomies()
    {
        Taxonomies\FilmTaxonomies::register();
        Taxonomies\ProgettiTaxonomies::register();
        Taxonomies\ProgettiDocTaxonomies::register();
        Taxonomies\ProgettiDramaTaxonomies::register();
        Taxonomies\ProposteEditorialiTaxonomies::register();
        Taxonomies\EventiTaxonomies::register();
        Taxonomies\WhosComingTaxonomies::register();
    }

    #[Action("init")]
    public function register_polylang_strings()
    {
        Translations::register();
    }

    #[Action("wp_enqueue_scripts")]
    public function enqueue_frontend_assets()
    {
        $vite = $this->vite;

        if (is_array($vite->manifest)) {
            if ($vite->environment === "production" || is_admin()) {
                $js_file = "src/main.js";
                wp_enqueue_style(
                    "main",
                    $vite->dist_uri . "/" . $vite->manifest[$js_file]["css"][0],
                );
                wp_enqueue_script(
                    "main",
                    $vite->dist_uri . "/" . $vite->manifest[$js_file]["file"],
                    [],
                    "",
                    [
                        "strategy" => "defer",
                        "in_footer" => true,
                    ],
                );
            }
        }

        if ($vite->environment === "development") {
            add_action("wp_head", function () use ($vite) {
                echo '<script type="module" crossorigin src="' .
                    $vite->dev_manifest["url"] .
                    '@vite/client"></script>';
                echo '<script type="module" crossorigin src="' .
                    $vite->dev_manifest["url"] .
                    'src/main.js"></script>';
            });
        }
    }

    #[Action("admin_enqueue_scripts")]
    public function enqueue_backend_assets()
    {
        $vite = $this->vite;
        $js_file = "src/admin.js";

        if (is_array($vite->manifest)) {
            if ($vite->environment === "production" || is_admin()) {
                wp_enqueue_style(
                    "admin",
                    $vite->dist_uri . "/" . $vite->manifest[$js_file]["css"][0],
                );
                wp_enqueue_script(
                    "admin",
                    $vite->dist_uri . "/" . $vite->manifest[$js_file]["file"],
                    [],
                    "",
                    [
                        "strategy" => "defer",
                        "in_footer" => true,
                    ],
                );
            }
        }

        if ($vite->environment === "development") {
            add_action("admin_head", function () use ($vite, $js_file) {
                echo '<script type="module" crossorigin src="' .
                    $vite->dev_manifest["url"] .
                    '@vite/client"></script>';
                echo '<script type="module" crossorigin src="' .
                    $vite->dev_manifest["url"] .
                    $js_file .
                    '"></script>';
            });
        }
    }

    /**
     * This is where you add some context
     *
     * @param string $context context['this'] Being the Twig's {{ this }}.
     */
    #[Filter("timber/context")]
    public function add_to_context($context)
    {
        $context["site"] = $this;
        $context["menu"] = Timber::get_menu();

        // Set all nav menus in context.
        foreach (array_keys(get_registered_nav_menus()) as $location) {
            // Bail out if menu has no location.
            if (!has_nav_menu($location)) {
                continue;
            }

            $menu = Timber::get_menu($location);
            $context["menus"][$location] = $menu;
        }

        $lang = function_exists("pll_current_language")
            ? pll_current_language()
            : "it";
        $context["menu_festival"] = Timber::get_menu("festival-{$lang}");
        $context["menu_industry"] = Timber::get_menu("industry-{$lang}");
        $context["menu_campus"] = Timber::get_menu("campus-{$lang}");
        $context["menu_submenu"] = Timber::get_menu("submenu-{$lang}");
        $context["current_url"] = URLHelper::get_current_url();
        $context["header_show_date_location"] = get_field(
            "header_show_date_location",
            "option",
        );
        $context["header_date"] =
            get_field("header_date", "option") ?: "5 — 15.06.2026";
        $context["header_location"] =
            get_field("header_location", "option") ?: "Bologna";
        $context["footer_image"] = get_field("footer_image", "option");
        $context["footer_image_mobile"] = get_field(
            "footer_image_mobile",
            "option",
        );
        $context["ragione_sociale"] = get_field("ragione_sociale", "option");
        $context["footer_bottom_links"] = get_field("footer_bottom_links", "option") ?: [];
        $context["footer_credits"] = get_field("footer_credits", "option");
        $context["social_instagram"] = get_field("social_instagram", "option");
        $context["social_facebook"] = get_field("social_facebook", "option");
        $context["social_youtube"] = get_field("social_youtube", "option");
        $context["social_tiktok"] = get_field("social_tiktok", "option");
        $context["social_x"] = get_field("social_x", "option");
        $context["social_linkedin"] = get_field("social_linkedin", "option");
        $context["social_telegram"] = get_field("social_telegram", "option");
        $context["social_industry_instagram"] = get_field("social_industry_instagram", "option");
        $context["social_industry_facebook"] = get_field("social_industry_facebook", "option");
        $context["social_industry_youtube"] = get_field("social_industry_youtube", "option");
        $context["social_industry_tiktok"] = get_field("social_industry_tiktok", "option");
        $context["social_industry_x"] = get_field("social_industry_x", "option");
        $context["social_industry_linkedin"] = get_field("social_industry_linkedin", "option");
        $context["social_industry_telegram"] = get_field("social_industry_telegram", "option");
        $context["newsletter_title"] = get_field("newsletter_title", "option");
        $context["newsletter_social_text"] = get_field("newsletter_social_text", "option");
        $context["newsletter_social_text_industry"] = get_field("newsletter_social_text_industry", "option");
        $context["newsletter_form_shortcode"] = get_field("newsletter_form_shortcode", "option");
        $context["mapbox_token"] = get_field("mapbox_api_key", "option");
        $context["environment"] = $this->vite->environment;

        if (function_exists("pll_the_languages")) {
            $context["languages"] = pll_the_languages(["raw" => 1]);
        }

        // Detect current section (festival = homepage, industry, campus)
        $section = "festival";
        global $post;
        if (is_page() && $post) {
            $ids = array_merge([$post->ID], get_post_ancestors($post->ID));
            foreach ($ids as $id) {
                $slug = get_post_field("post_name", $id);
                if (in_array($slug, ["industry", "campus"])) {
                    $section = $slug;
                    break;
                }
            }
        } elseif (
            is_post_type_archive(["progetto", "evento"]) ||
            is_singular(["progetto", "evento"])
        ) {
            $section = "campus";
        } elseif (
            is_post_type_archive("whos-coming") ||
            is_singular("whos-coming") ||
            is_post_type_archive("contents-doc") ||
            is_singular("contents-doc") ||
            is_post_type_archive("contents-drama") ||
            is_singular("contents-drama") ||
            is_singular("progetti-doc") ||
            is_singular("progetti-drama") ||
            is_singular("attivita-doc") ||
            is_singular("attivita-drama") ||
            is_singular("producers") ||
            is_post_type_archive("producers") ||
            is_singular("publishers") ||
            is_post_type_archive("publishers") ||
            is_singular("proposte-editoriali")
        ) {
            $section = "industry";
        }
        $context["current_section"] = $section;

        // Build breadcrumbs from page hierarchy
        $breadcrumbs = [];
        if (is_search()) {
            $crumb_home = function_exists("pll_home_url") ? pll_home_url() : home_url("/");
            $breadcrumbs[] = ["url" => $crumb_home, "title" => "Biografilm"];
            $breadcrumbs[] = ["url" => "", "title" => function_exists("pll__") ? pll__("Risultati") : "Risultati"];
        } elseif (is_404()) {
            $crumb_home = function_exists("pll_home_url") ? pll_home_url() : home_url("/");
            $breadcrumbs[] = ["url" => $crumb_home, "title" => "Biografilm"];
            $breadcrumbs[] = ["url" => "", "title" => "404"];
        } elseif (is_post_type_archive("sezione")) {
            $breadcrumbs[] = ["url" => home_url("/"), "title" => "Festival"];
            $breadcrumbs[] = [
                "url" => "",
                "title" => function_exists("pll__")
                    ? pll__("Sezioni")
                    : "Sezioni",
            ];
        } elseif (is_post_type_archive("film")) {
            $breadcrumbs[] = ["url" => home_url("/"), "title" => "Festival"];
            $breadcrumbs[] = [
                "url" => "",
                "title" => function_exists("pll__")
                    ? pll__("Tutti i film")
                    : "Tutti i film",
            ];
        } elseif (is_post_type_archive("news")) {
            $breadcrumbs[] = ["url" => home_url("/"), "title" => "Biografilm"];
            $breadcrumbs[] = ["url" => "", "title" => "News"];
        } elseif (is_post_type_archive("ospitalita")) {
            $breadcrumbs[] = ["url" => home_url("/"), "title" => "Biografilm"];
            $breadcrumbs[] = ["url" => "", "title" => "Ospitalità"];
        } elseif (is_post_type_archive("whos-coming")) {
            $industry_page = get_page_by_path("industry");
            if ($industry_page && function_exists("pll_get_post")) {
                $translated = pll_get_post($industry_page->ID);
                if ($translated) {
                    $industry_page = get_post($translated);
                }
            }
            $crumb_home = function_exists("pll_home_url")
                ? pll_home_url()
                : home_url("/");
            $breadcrumbs[] = ["url" => $crumb_home, "title" => "Biografilm"];
            $breadcrumbs[] = [
                "url" => $industry_page
                    ? get_permalink($industry_page)
                    : $crumb_home,
                "title" => "Industry",
            ];
            $breadcrumbs[] = ["url" => "", "title" => "Who's Coming"];
        } elseif (is_post_type_archive("contents-doc")) {
            $industry_page = get_page_by_path("industry");
            $bio_to_bdoc_page = get_page_by_path("industry/bio-to-b-doc");
            if (function_exists("pll_get_post")) {
                if ($industry_page && ($t = pll_get_post($industry_page->ID))) {
                    $industry_page = get_post($t);
                }
                if ($bio_to_bdoc_page && ($t = pll_get_post($bio_to_bdoc_page->ID))) {
                    $bio_to_bdoc_page = get_post($t);
                }
            }
            $crumb_home = function_exists("pll_home_url") ? pll_home_url() : home_url("/");
            $breadcrumbs[] = ["url" => $crumb_home, "title" => "Biografilm"];
            $breadcrumbs[] = [
                "url" => $industry_page ? get_permalink($industry_page) : $crumb_home,
                "title" => "Industry",
            ];
            $breadcrumbs[] = [
                "url" => $bio_to_bdoc_page ? get_permalink($bio_to_bdoc_page) : "",
                "title" => "Bio to B | Doc",
            ];
            $breadcrumbs[] = ["url" => "", "title" => "Contents"];
        } elseif (is_post_type_archive("contents-drama")) {
            $industry_page = get_page_by_path("industry");
            $bio_to_bdrama_page = get_page_by_path("industry/bio-to-b-drama");
            if (function_exists("pll_get_post")) {
                if ($industry_page && ($t = pll_get_post($industry_page->ID))) {
                    $industry_page = get_post($t);
                }
                if ($bio_to_bdrama_page && ($t = pll_get_post($bio_to_bdrama_page->ID))) {
                    $bio_to_bdrama_page = get_post($t);
                }
            }
            $crumb_home = function_exists("pll_home_url") ? pll_home_url() : home_url("/");
            $breadcrumbs[] = ["url" => $crumb_home, "title" => "Biografilm"];
            $breadcrumbs[] = [
                "url" => $industry_page ? get_permalink($industry_page) : $crumb_home,
                "title" => "Industry",
            ];
            $breadcrumbs[] = [
                "url" => $bio_to_bdrama_page ? get_permalink($bio_to_bdrama_page) : "",
                "title" => "Bio to B | Drama",
            ];
            $breadcrumbs[] = ["url" => "", "title" => "Contents"];
        } elseif (is_post_type_archive("producers")) {
            $industry_page = get_page_by_path("industry");
            $bio_to_bdrama_page = get_page_by_path("industry/bio-to-b-drama");
            if (function_exists("pll_get_post")) {
                if ($industry_page && ($t = pll_get_post($industry_page->ID))) {
                    $industry_page = get_post($t);
                }
                if ($bio_to_bdrama_page && ($t = pll_get_post($bio_to_bdrama_page->ID))) {
                    $bio_to_bdrama_page = get_post($t);
                }
            }
            $crumb_home = function_exists("pll_home_url") ? pll_home_url() : home_url("/");
            $breadcrumbs[] = ["url" => $crumb_home, "title" => "Biografilm"];
            $breadcrumbs[] = [
                "url" => $industry_page ? get_permalink($industry_page) : $crumb_home,
                "title" => "Industry",
            ];
            $breadcrumbs[] = [
                "url" => $bio_to_bdrama_page ? get_permalink($bio_to_bdrama_page) : "",
                "title" => "Bio to B | Drama",
            ];
            $breadcrumbs[] = [
                "url" => get_post_type_archive_link("contents-drama"),
                "title" => "Contents",
            ];
            $breadcrumbs[] = ["url" => "", "title" => "Producers"];
        } elseif (is_post_type_archive("publishers")) {
            $industry_page = get_page_by_path("industry");
            $bio_to_bdrama_page = get_page_by_path("industry/bio-to-b-drama");
            if (function_exists("pll_get_post")) {
                if ($industry_page && ($t = pll_get_post($industry_page->ID))) {
                    $industry_page = get_post($t);
                }
                if ($bio_to_bdrama_page && ($t = pll_get_post($bio_to_bdrama_page->ID))) {
                    $bio_to_bdrama_page = get_post($t);
                }
            }
            $crumb_home = function_exists("pll_home_url") ? pll_home_url() : home_url("/");
            $breadcrumbs[] = ["url" => $crumb_home, "title" => "Biografilm"];
            $breadcrumbs[] = [
                "url" => $industry_page ? get_permalink($industry_page) : $crumb_home,
                "title" => "Industry",
            ];
            $breadcrumbs[] = [
                "url" => $bio_to_bdrama_page ? get_permalink($bio_to_bdrama_page) : "",
                "title" => "Bio to B | Drama",
            ];
            $breadcrumbs[] = [
                "url" => get_post_type_archive_link("contents-drama"),
                "title" => "Contents",
            ];
            $breadcrumbs[] = ["url" => "", "title" => "Publishers"];
        } elseif (is_post_type_archive("evento")) {
            $campus_page = get_page_by_path("campus");
            if ($campus_page && function_exists("pll_get_post")) {
                $translated = pll_get_post($campus_page->ID);
                if ($translated) {
                    $campus_page = get_post($translated);
                }
            }
            $crumb_home = function_exists("pll_home_url")
                ? pll_home_url()
                : home_url("/");
            $breadcrumbs[] = ["url" => $crumb_home, "title" => "Biografilm"];
            $breadcrumbs[] = [
                "url" => $campus_page
                    ? get_permalink($campus_page)
                    : $crumb_home,
                "title" => "Campus",
            ];
            $breadcrumbs[] = [
                "url" => "",
                "title" => function_exists("pll__")
                    ? pll__("Eventi")
                    : "Eventi",
            ];
        } elseif (is_post_type_archive("progetto")) {
            $campus_page = get_page_by_path("campus");
            if ($campus_page && function_exists("pll_get_post")) {
                $translated = pll_get_post($campus_page->ID);
                if ($translated) {
                    $campus_page = get_post($translated);
                }
            }
            $crumb_home = function_exists("pll_home_url")
                ? pll_home_url()
                : home_url("/");
            $breadcrumbs[] = ["url" => $crumb_home, "title" => "Biografilm"];
            $breadcrumbs[] = [
                "url" => $campus_page
                    ? get_permalink($campus_page)
                    : $crumb_home,
                "title" => "Campus",
            ];
            $breadcrumbs[] = [
                "url" => "",
                "title" => function_exists("pll__")
                    ? pll__("Progetti e formazione")
                    : "Projects and education",
            ];
        } elseif ($post && !is_front_page()) {
            if (get_post_type($post->ID) === "news") {
                $breadcrumbs[] = [
                    "url" => home_url("/"),
                    "title" => "Biografilm",
                ];
                $breadcrumbs[] = [
                    "url" => get_post_type_archive_link("news"),
                    "title" => "News",
                ];
                $breadcrumbs[] = [
                    "url" => "",
                    "title" => get_the_title($post->ID),
                ];
            } elseif (get_post_type($post->ID) === "producers") {
                $industry_page = get_page_by_path("industry");
                $bio_to_bdrama_page = get_page_by_path("industry/bio-to-b-drama");
                $producers_page =
                    get_page_by_path("industry/bio-to-b-drama/producers") ?:
                    get_page_by_path("producers");
                if (function_exists("pll_get_post")) {
                    if ($industry_page && ($t = pll_get_post($industry_page->ID))) {
                        $industry_page = get_post($t);
                    }
                    if ($bio_to_bdrama_page && ($t = pll_get_post($bio_to_bdrama_page->ID))) {
                        $bio_to_bdrama_page = get_post($t);
                    }
                    if ($producers_page && ($t = pll_get_post($producers_page->ID))) {
                        $producers_page = get_post($t);
                    }
                }
                $crumb_home = function_exists("pll_home_url") ? pll_home_url() : home_url("/");
                $breadcrumbs[] = ["url" => $crumb_home, "title" => "Biografilm"];
                $breadcrumbs[] = [
                    "url" => $industry_page ? get_permalink($industry_page) : $crumb_home,
                    "title" => "Industry",
                ];
                $breadcrumbs[] = [
                    "url" => $bio_to_bdrama_page ? get_permalink($bio_to_bdrama_page) : "",
                    "title" => "Bio to B | Drama",
                ];
                $breadcrumbs[] = [
                    "url" => get_post_type_archive_link("contents-drama"),
                    "title" => "Contents",
                ];
                $breadcrumbs[] = [
                    "url" => $producers_page
                        ? get_permalink($producers_page)
                        : get_post_type_archive_link("producers"),
                    "title" => "Producers",
                ];
                $breadcrumbs[] = ["url" => "", "title" => get_the_title($post->ID)];
            } elseif (get_post_type($post->ID) === "publishers") {
                $industry_page = get_page_by_path("industry");
                $bio_to_bdrama_page = get_page_by_path("industry/bio-to-b-drama");
                $publishers_page =
                    get_page_by_path("industry/bio-to-b-drama/publishers") ?:
                    get_page_by_path("publishers");
                if (function_exists("pll_get_post")) {
                    if ($industry_page && ($t = pll_get_post($industry_page->ID))) {
                        $industry_page = get_post($t);
                    }
                    if ($bio_to_bdrama_page && ($t = pll_get_post($bio_to_bdrama_page->ID))) {
                        $bio_to_bdrama_page = get_post($t);
                    }
                    if ($publishers_page && ($t = pll_get_post($publishers_page->ID))) {
                        $publishers_page = get_post($t);
                    }
                }
                $crumb_home = function_exists("pll_home_url") ? pll_home_url() : home_url("/");
                $breadcrumbs[] = ["url" => $crumb_home, "title" => "Biografilm"];
                $breadcrumbs[] = [
                    "url" => $industry_page ? get_permalink($industry_page) : $crumb_home,
                    "title" => "Industry",
                ];
                $breadcrumbs[] = [
                    "url" => $bio_to_bdrama_page ? get_permalink($bio_to_bdrama_page) : "",
                    "title" => "Bio to B | Drama",
                ];
                $breadcrumbs[] = [
                    "url" => get_post_type_archive_link("contents-drama"),
                    "title" => "Contents",
                ];
                $breadcrumbs[] = [
                    "url" => $publishers_page
                        ? get_permalink($publishers_page)
                        : get_post_type_archive_link("publishers"),
                    "title" => "Publishers",
                ];
                $breadcrumbs[] = ["url" => "", "title" => get_the_title($post->ID)];
            } elseif (get_post_type($post->ID) === "film") {
                $breadcrumbs[] = [
                    "url" => home_url("/"),
                    "title" => "Festival",
                ];
                $breadcrumbs[] = [
                    "url" => get_post_type_archive_link("film"),
                    "title" => function_exists("pll__")
                        ? pll__("Tutti i film")
                        : "Tutti i film",
                ];
                $breadcrumbs[] = [
                    "url" => "",
                    "title" => get_the_title($post->ID),
                ];
            } elseif (get_post_type($post->ID) === "sezione") {
                $breadcrumbs[] = [
                    "url" => home_url("/"),
                    "title" => "Festival",
                ];
                $breadcrumbs[] = [
                    "url" => get_post_type_archive_link("sezione"),
                    "title" => function_exists("pll__")
                        ? pll__("Sezioni")
                        : "Sezioni",
                ];
                $breadcrumbs[] = [
                    "url" => "",
                    "title" => get_the_title($post->ID),
                ];
            } elseif (get_post_type($post->ID) === "ospitalita") {
                $ospitalita_page = get_page_by_path("ospitality");
                $breadcrumbs[] = [
                    "url" => home_url("/"),
                    "title" => "Biografilm",
                ];
                $breadcrumbs[] = [
                    "url" => $ospitalita_page
                        ? get_permalink($ospitalita_page)
                        : home_url("/"),
                    "title" => "Ospitalità",
                ];
                $breadcrumbs[] = [
                    "url" => "",
                    "title" => get_the_title($post->ID),
                ];
            } elseif (get_post_type($post->ID) === "partner") {
                $breadcrumbs[] = [
                    "url" => home_url("/"),
                    "title" => "Biografilm",
                ];
                $breadcrumbs[] = [
                    "url" => get_permalink(get_page_by_path("partners")),
                    "title" => "Partners",
                ];
                $breadcrumbs[] = [
                    "url" => "",
                    "title" => get_the_title($post->ID),
                ];
            } elseif (get_post_type($post->ID) === "evento") {
                $campus_page = get_page_by_path("campus");
                if ($campus_page && function_exists("pll_get_post")) {
                    $translated = pll_get_post($campus_page->ID);
                    if ($translated) {
                        $campus_page = get_post($translated);
                    }
                }
                $crumb_home = function_exists("pll_home_url")
                    ? pll_home_url()
                    : home_url("/");
                $breadcrumbs[] = [
                    "url" => $crumb_home,
                    "title" => "Biografilm",
                ];
                $breadcrumbs[] = [
                    "url" => $campus_page
                        ? get_permalink($campus_page)
                        : $crumb_home,
                    "title" => "Campus",
                ];
                $breadcrumbs[] = [
                    "url" => get_post_type_archive_link("evento"),
                    "title" => function_exists("pll__")
                        ? pll__("Eventi")
                        : "Events",
                ];
                $breadcrumbs[] = [
                    "url" => "",
                    "title" => get_the_title($post->ID),
                ];
            } elseif (get_post_type($post->ID) === "progetto") {
                $campus_page = get_page_by_path("campus");
                if ($campus_page && function_exists("pll_get_post")) {
                    $translated = pll_get_post($campus_page->ID);
                    if ($translated) {
                        $campus_page = get_post($translated);
                    }
                }
                $crumb_home = function_exists("pll_home_url")
                    ? pll_home_url()
                    : home_url("/");
                $breadcrumbs[] = [
                    "url" => $crumb_home,
                    "title" => "Biografilm",
                ];
                $breadcrumbs[] = [
                    "url" => $campus_page
                        ? get_permalink($campus_page)
                        : $crumb_home,
                    "title" => "Campus",
                ];
                $breadcrumbs[] = [
                    "url" => get_post_type_archive_link("progetto"),
                    "title" => function_exists("pll__")
                        ? pll__("Progetti e formazione")
                        : "Projects and education",
                ];
                $breadcrumbs[] = [
                    "url" => "",
                    "title" => get_the_title($post->ID),
                ];
            } elseif (get_post_type($post->ID) === "whos-coming") {
                $industry_page = get_page_by_path("industry");
                $breadcrumbs[] = [
                    "url" => home_url("/"),
                    "title" => "Biografilm",
                ];
                $breadcrumbs[] = [
                    "url" => $industry_page
                        ? get_permalink($industry_page)
                        : home_url("/"),
                    "title" => "Industry",
                ];
                $breadcrumbs[] = [
                    "url" => get_post_type_archive_link("whos-coming"),
                    "title" => "Who's Coming",
                ];
                $breadcrumbs[] = [
                    "url" => "",
                    "title" => get_the_title($post->ID),
                ];
            } elseif (get_post_type($post->ID) === "contents-doc") {
                $industry_page = get_page_by_path("industry");
                $bio_to_bdoc_page = get_page_by_path("industry/bio-to-b-doc");
                if (function_exists("pll_get_post")) {
                    if ($industry_page && ($t = pll_get_post($industry_page->ID))) {
                        $industry_page = get_post($t);
                    }
                    if ($bio_to_bdoc_page && ($t = pll_get_post($bio_to_bdoc_page->ID))) {
                        $bio_to_bdoc_page = get_post($t);
                    }
                }
                $crumb_home = function_exists("pll_home_url") ? pll_home_url() : home_url("/");
                $breadcrumbs[] = ["url" => $crumb_home, "title" => "Biografilm"];
                $breadcrumbs[] = [
                    "url" => $industry_page ? get_permalink($industry_page) : $crumb_home,
                    "title" => "Industry",
                ];
                $breadcrumbs[] = [
                    "url" => $bio_to_bdoc_page ? get_permalink($bio_to_bdoc_page) : "",
                    "title" => "Bio to B | Doc",
                ];
                $breadcrumbs[] = [
                    "url" => get_post_type_archive_link("contents-doc"),
                    "title" => "Contents",
                ];
                $breadcrumbs[] = ["url" => "", "title" => get_the_title($post->ID)];
            } elseif (get_post_type($post->ID) === "contents-drama") {
                $industry_page = get_page_by_path("industry");
                $bio_to_bdrama_page = get_page_by_path("industry/bio-to-b-drama");
                if (function_exists("pll_get_post")) {
                    if ($industry_page && ($t = pll_get_post($industry_page->ID))) {
                        $industry_page = get_post($t);
                    }
                    if ($bio_to_bdrama_page && ($t = pll_get_post($bio_to_bdrama_page->ID))) {
                        $bio_to_bdrama_page = get_post($t);
                    }
                }
                $crumb_home = function_exists("pll_home_url") ? pll_home_url() : home_url("/");
                $breadcrumbs[] = ["url" => $crumb_home, "title" => "Biografilm"];
                $breadcrumbs[] = [
                    "url" => $industry_page ? get_permalink($industry_page) : $crumb_home,
                    "title" => "Industry",
                ];
                $breadcrumbs[] = [
                    "url" => $bio_to_bdrama_page ? get_permalink($bio_to_bdrama_page) : "",
                    "title" => "Bio to B | Drama",
                ];
                $breadcrumbs[] = [
                    "url" => get_post_type_archive_link("contents-drama"),
                    "title" => "Contents",
                ];
                $breadcrumbs[] = ["url" => "", "title" => get_the_title($post->ID)];
            } elseif (get_post_type($post->ID) === "progetti-doc") {
                $industry_page = get_page_by_path("industry");
                $bio_to_bdoc_page = get_page_by_path("industry/bio-to-b-doc");
                $breadcrumbs[] = [
                    "url" => home_url("/"),
                    "title" => "Biografilm",
                ];
                $breadcrumbs[] = [
                    "url" => $industry_page
                        ? get_permalink($industry_page)
                        : home_url("/"),
                    "title" => "Industry",
                ];
                $breadcrumbs[] = [
                    "url" => $bio_to_bdoc_page
                        ? get_permalink($bio_to_bdoc_page)
                        : "",
                    "title" => "Bio to B | Doc",
                ];
                $breadcrumbs[] = [
                    "url" => get_post_type_archive_link("contents-doc"),
                    "title" => "Contents",
                ];
                $contents_doc_parent = get_field("contents_doc_parent", $post->ID);
                if ($contents_doc_parent) {
                    $breadcrumbs[] = [
                        "url" => get_permalink($contents_doc_parent->ID),
                        "title" => get_the_title($contents_doc_parent->ID),
                    ];
                }
                $breadcrumbs[] = [
                    "url" => "",
                    "title" => get_the_title($post->ID),
                ];
            } elseif (get_post_type($post->ID) === "progetti-drama") {
                $industry_page = get_page_by_path("industry");
                $bio_to_bdrama_page = get_page_by_path(
                    "industry/bio-to-b-drama",
                );
                $breadcrumbs[] = [
                    "url" => home_url("/"),
                    "title" => "Biografilm",
                ];
                $breadcrumbs[] = [
                    "url" => $industry_page
                        ? get_permalink($industry_page)
                        : home_url("/"),
                    "title" => "Industry",
                ];
                $breadcrumbs[] = [
                    "url" => $bio_to_bdrama_page
                        ? get_permalink($bio_to_bdrama_page)
                        : "",
                    "title" => "Bio to B | Drama",
                ];
                $breadcrumbs[] = [
                    "url" => get_post_type_archive_link("contents-drama"),
                    "title" => "Contents",
                ];
                $contents_drama_parent = get_field("contents_drama_parent", $post->ID);
                if ($contents_drama_parent) {
                    $breadcrumbs[] = [
                        "url" => get_permalink($contents_drama_parent->ID),
                        "title" => get_the_title($contents_drama_parent->ID),
                    ];
                }
                $breadcrumbs[] = [
                    "url" => "",
                    "title" => get_the_title($post->ID),
                ];
            } elseif (get_post_type($post->ID) === "attivita-doc") {
                $industry_page = get_page_by_path("industry");
                $bio_to_bdoc_page = get_page_by_path("industry/bio-to-b-doc");
                if (function_exists("pll_get_post")) {
                    if ($industry_page && ($t = pll_get_post($industry_page->ID))) {
                        $industry_page = get_post($t);
                    }
                    if ($bio_to_bdoc_page && ($t = pll_get_post($bio_to_bdoc_page->ID))) {
                        $bio_to_bdoc_page = get_post($t);
                    }
                }
                $crumb_home = function_exists("pll_home_url") ? pll_home_url() : home_url("/");
                $breadcrumbs[] = ["url" => $crumb_home, "title" => "Biografilm"];
                $breadcrumbs[] = [
                    "url" => $industry_page ? get_permalink($industry_page) : $crumb_home,
                    "title" => "Industry",
                ];
                $breadcrumbs[] = [
                    "url" => $bio_to_bdoc_page ? get_permalink($bio_to_bdoc_page) : "",
                    "title" => "Bio to B | Doc",
                ];
                $breadcrumbs[] = [
                    "url" => get_post_type_archive_link("contents-doc"),
                    "title" => "Contents",
                ];
                $breadcrumbs[] = ["url" => "", "title" => get_the_title($post->ID)];
            } elseif (get_post_type($post->ID) === "attivita-drama") {
                $industry_page = get_page_by_path("industry");
                $bio_to_bdrama_page = get_page_by_path("industry/bio-to-b-drama");
                if (function_exists("pll_get_post")) {
                    if ($industry_page && ($t = pll_get_post($industry_page->ID))) {
                        $industry_page = get_post($t);
                    }
                    if ($bio_to_bdrama_page && ($t = pll_get_post($bio_to_bdrama_page->ID))) {
                        $bio_to_bdrama_page = get_post($t);
                    }
                }
                $crumb_home = function_exists("pll_home_url") ? pll_home_url() : home_url("/");
                $breadcrumbs[] = ["url" => $crumb_home, "title" => "Biografilm"];
                $breadcrumbs[] = [
                    "url" => $industry_page ? get_permalink($industry_page) : $crumb_home,
                    "title" => "Industry",
                ];
                $breadcrumbs[] = [
                    "url" => $bio_to_bdrama_page ? get_permalink($bio_to_bdrama_page) : "",
                    "title" => "Bio to B | Drama",
                ];
                $breadcrumbs[] = [
                    "url" => get_post_type_archive_link("contents-drama"),
                    "title" => "Contents",
                ];
                $breadcrumbs[] = ["url" => "", "title" => get_the_title($post->ID)];
            } elseif (get_post_type($post->ID) === "proposte-editoriali") {
                $industry_page = get_page_by_path("industry");
                $bio_to_bdrama_page = get_page_by_path("industry/bio-to-b-drama");
                $publishers_page =
                    get_page_by_path("industry/bio-to-b-drama/publishers") ?:
                    get_page_by_path("publishers");
                if (function_exists("pll_get_post")) {
                    if ($industry_page && ($t = pll_get_post($industry_page->ID))) {
                        $industry_page = get_post($t);
                    }
                    if ($bio_to_bdrama_page && ($t = pll_get_post($bio_to_bdrama_page->ID))) {
                        $bio_to_bdrama_page = get_post($t);
                    }
                    if ($publishers_page && ($t = pll_get_post($publishers_page->ID))) {
                        $publishers_page = get_post($t);
                    }
                }
                $editore = get_field("editore", $post->ID);
                $crumb_home = function_exists("pll_home_url") ? pll_home_url() : home_url("/");
                $breadcrumbs[] = ["url" => $crumb_home, "title" => "Biografilm"];
                $breadcrumbs[] = [
                    "url" => $industry_page ? get_permalink($industry_page) : $crumb_home,
                    "title" => "Industry",
                ];
                $breadcrumbs[] = [
                    "url" => $bio_to_bdrama_page ? get_permalink($bio_to_bdrama_page) : "",
                    "title" => "Bio to B | Drama",
                ];
                $breadcrumbs[] = [
                    "url" => get_post_type_archive_link("contents-drama"),
                    "title" => "Contents",
                ];
                $breadcrumbs[] = [
                    "url" => $publishers_page
                        ? get_permalink($publishers_page)
                        : get_post_type_archive_link("publishers"),
                    "title" => "Publishers",
                ];
                if ($editore) {
                    $breadcrumbs[] = [
                        "url" => get_permalink($editore->ID),
                        "title" => get_the_title($editore->ID),
                    ];
                }
                $breadcrumbs[] = ["url" => "", "title" => get_the_title($post->ID)];
            } else {
                $ancestors = get_post_ancestors($post->ID);
                $home_label =
                    empty($ancestors) ||
                    in_array($section, ["industry", "campus"])
                        ? "Biografilm"
                        : "Festival";
                $breadcrumbs[] = [
                    "url" => home_url("/"),
                    "title" => $home_label,
                ];
                $front_page_id = (int) get_option("page_on_front");
                foreach (array_reverse($ancestors) as $ancestor_id) {
                    if ($ancestor_id === $front_page_id) {
                        continue;
                    }
                    $breadcrumbs[] = [
                        "url" => get_permalink($ancestor_id),
                        "title" => get_the_title($ancestor_id),
                    ];
                }
                $breadcrumbs[] = [
                    "url" => "",
                    "title" => get_the_title($post->ID),
                ];
            }
        }
        $context["breadcrumbs"] = $breadcrumbs;

        if (is_singular("news") && $post) {
            $terms = wp_get_post_terms($post->ID, "news-category", [
                "fields" => "ids",
            ]);
            $related = [];

            if (!empty($terms) && !is_wp_error($terms)) {
                $related = get_posts([
                    "post_type" => "news",
                    "posts_per_page" => 3,
                    "post__not_in" => [$post->ID],
                    "orderby" => "date",
                    "order" => "DESC",
                    "tax_query" => [
                        [
                            "taxonomy" => "news-category",
                            "field" => "term_id",
                            "terms" => $terms,
                        ],
                    ],
                ]);
            }

            // Fall back to latest news if not enough results from category
            if (count($related) < 3) {
                $exclude = array_merge(
                    [$post->ID],
                    array_map(fn($p) => $p->ID, $related),
                );
                $fallback = get_posts([
                    "post_type" => "news",
                    "posts_per_page" => 3 - count($related),
                    "post__not_in" => $exclude,
                    "orderby" => "date",
                    "order" => "DESC",
                ]);
                $related = array_merge($related, $fallback);
            }

            $context["related_news"] = array_map(
                fn($p) => Timber::get_post($p->ID),
                $related,
            );
        }

        if (is_singular("ospitalita") && $post) {
            $related = get_posts([
                "post_type" => "ospitalita",
                "posts_per_page" => 3,
                "post__not_in" => [$post->ID],
                "orderby" => "date",
                "order" => "DESC",
            ]);
            $context["related_ospitalita"] = array_map(
                fn($p) => Timber::get_post($p->ID),
                $related,
            );
        }

        if (is_singular("film") && $post) {
            $manual = get_field("film_correlati", $post->ID);
            $ids = !empty($manual)
                ? array_map(
                    fn($p) => is_object($p) ? $p->ID : (int) $p,
                    $manual,
                )
                : [];
            if (count($ids) < 3) {
                $ids = array_merge(
                    $ids,
                    self::get_related_films($post->ID, 3 - count($ids), $ids),
                );
            }
            $context["related_films"] = array_map(
                fn($id) => Timber::get_post($id),
                $ids,
            );
        }

        $industry_page = get_page_by_path("industry");
        $campus_page = get_page_by_path("campus");
        if (function_exists("pll_get_post")) {
            if ($industry_page) {
                $translated = pll_get_post($industry_page->ID);
                if ($translated) {
                    $industry_page = get_post($translated);
                }
            }
            if ($campus_page) {
                $translated = pll_get_post($campus_page->ID);
                if ($translated) {
                    $campus_page = get_post($translated);
                }
            }
        }
        $home_url = function_exists("pll_home_url")
            ? pll_home_url()
            : home_url("/");
        $context["home_url"] = $home_url;
        $festival_link = get_field("header_link_festival", "option");
        $industry_link = get_field("header_link_industry", "option");
        $campus_link   = get_field("header_link_campus", "option");
        $context["nav_urls"] = [
            "festival" => $festival_link["url"] ?? $home_url,
            "industry" => $industry_link["url"] ?? ($industry_page
                ? get_permalink($industry_page->ID)
                : home_url("/industry/")),
            "campus"   => $campus_link["url"] ?? ($campus_page
                ? get_permalink($campus_page->ID)
                : home_url("/campus/")),
        ];
        $context["nav_labels"] = [
            "festival" => ($festival_link["title"] ?? null) ?: "Festival",
            "industry" => ($industry_link["title"] ?? null) ?: "Industry",
            "campus"   => ($campus_link["title"] ?? null) ?: "Campus",
        ];

        return $context;
    }

    #[Action("after_setup_theme")]
    public function theme_supports()
    {
        // Add default posts and comments RSS feed links to head.
        add_theme_support("automatic-feed-links");

        /*
         * Let WordPress manage the document title.
         * By adding theme support, we declare that this theme does not use a
         * hard-coded <title> tag in the document head, and expect WordPress to
         * provide it for us.
         */
        add_theme_support("title-tag");

        /*
         * Enable support for Post Thumbnails on posts and pages.
         *
         * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
         */
        add_theme_support("post-thumbnails");

        /*
         * Switch default core markup for search form, comment form, and comments
         * to output valid HTML5.
         */
        add_theme_support("html5", [
            "comment-form",
            "comment-list",
            "gallery",
            "caption",
        ]);

        /*
         * Enable support for Post Formats.
         *
         * See: https://codex.wordpress.org/Post_Formats
         */
        add_theme_support("post-formats", [
            "aside",
            "image",
            "video",
            "quote",
            "link",
            "gallery",
            "audio",
        ]);

        add_theme_support("menus");
    }

    /**
     * This is where you can add your own functions to twig.
     *
     * @param Twig\Environment $twig get extension.
     */
    #[Filter("timber/twig")]
    public function add_to_twig($twig)
    {
        $twig->addExtension(new HatemlExtension());
        $twig->addExtension(new \Twig\Extra\Html\HtmlExtension());
        $twig->addRuntimeLoader(
            new \Twig\RuntimeLoader\FactoryRuntimeLoader([
                TailwindRuntime::class => fn() => new TailwindRuntime(),
            ]),
        );
        $twig->addExtension(new TailwindExtension());

        $twig->addFilter(
            new \Twig\TwigFilter("ray", function (...$params) {
                ray(...$params);
            }),
        );
        $twig->addFilter(
            new \Twig\TwigFilter("it_day", function (string $date): string {
                $ts = strtotime($date);
                $lang = function_exists("pll_current_language")
                    ? pll_current_language()
                    : "it";
                if ($lang === "en") {
                    $days = ["SUN", "MON", "TUE", "WED", "THU", "FRI", "SAT"];
                } else {
                    $days = ["DOM", "LUN", "MAR", "MER", "GIO", "VEN", "SAB"];
                }
                return $days[(int) date("w", $ts)] . " " . date("j", $ts);
            }),
        );
        $twig->addFilter(
            new \Twig\TwigFilter("it_date_long", function (
                string $date,
            ): string {
                $ts = strtotime($date);
                $lang = function_exists("pll_current_language")
                    ? pll_current_language()
                    : "it";
                if ($lang === "en") {
                    $months = [
                        "January",
                        "February",
                        "March",
                        "April",
                        "May",
                        "June",
                        "July",
                        "August",
                        "September",
                        "October",
                        "November",
                        "December",
                    ];
                } else {
                    $months = [
                        "Gennaio",
                        "Febbraio",
                        "Marzo",
                        "Aprile",
                        "Maggio",
                        "Giugno",
                        "Luglio",
                        "Agosto",
                        "Settembre",
                        "Ottobre",
                        "Novembre",
                        "Dicembre",
                    ];
                }
                return date("j", $ts) .
                    " " .
                    $months[(int) date("n", $ts) - 1] .
                    " " .
                    date("Y", $ts);
            }),
        );
        $twig->addFilter(
            new \Twig\TwigFilter("it_day_full", function (
                string $date,
            ): string {
                $ts = strtotime($date);
                $lang = function_exists("pll_current_language")
                    ? pll_current_language()
                    : "it";
                if ($lang === "en") {
                    $days = [
                        "Sunday",
                        "Monday",
                        "Tuesday",
                        "Wednesday",
                        "Thursday",
                        "Friday",
                        "Saturday",
                    ];
                    $months = [
                        "January",
                        "February",
                        "March",
                        "April",
                        "May",
                        "June",
                        "July",
                        "August",
                        "September",
                        "October",
                        "November",
                        "December",
                    ];
                } else {
                    $days = [
                        "Domenica",
                        "Lunedì",
                        "Martedì",
                        "Mercoledì",
                        "Giovedì",
                        "Venerdì",
                        "Sabato",
                    ];
                    $months = [
                        "Gennaio",
                        "Febbraio",
                        "Marzo",
                        "Aprile",
                        "Maggio",
                        "Giugno",
                        "Luglio",
                        "Agosto",
                        "Settembre",
                        "Ottobre",
                        "Novembre",
                        "Dicembre",
                    ];
                }
                return $days[(int) date("w", $ts)] .
                    " " .
                    date("j", $ts) .
                    " " .
                    $months[(int) date("n", $ts) - 1];
            }),
        );
        $twig->addFunction(
            new \Twig\TwigFunction("ray", function (...$params) {
                ray(...$params);
            }),
        );
        $twig->addFunction(
            new \Twig\TwigFunction("get_whoscoming_random", function (
                mixed $term_ids = null,
            ) {
                $args = [
                    "post_type" => "whos-coming",
                    "numberposts" => 4,
                    "orderby" => "rand",
                ];
                $ids = array_filter((array) ($term_ids ?? []));
                if (!empty($ids)) {
                    $args["tax_query"] = [
                        [
                            "taxonomy" => "accredito-whos-coming",
                            "field" => "term_id",
                            "terms" => $ids,
                        ],
                    ];
                }
                return get_posts($args);
            }),
        );
        $twig->addFunction(
            new \Twig\TwigFunction("get_publishers_random", function () {
                return get_posts([
                    "post_type" => "publishers",
                    "numberposts" => 4,
                    "orderby" => "rand",
                ]);
            }),
        );
        $twig->addFunction(
            new \Twig\TwigFunction("get_proposte_by_publisher", function (
                int $publisher_id,
            ) {
                return get_posts([
                    "post_type" => "proposte-editoriali",
                    "posts_per_page" => -1,
                    "orderby" => "title",
                    "order" => "ASC",
                    "meta_query" => [
                        [
                            "key" => "editore",
                            "value" => $publisher_id,
                            "compare" => "=",
                        ],
                    ],
                ]);
            }),
        );
        $twig->addFunction(
            new \Twig\TwigFunction("get_producers_random", function () {
                return get_posts([
                    "post_type" => "producers",
                    "numberposts" => 4,
                    "orderby" => "rand",
                ]);
            }),
        );
        return $twig;
    }

    /**
     * Updates Twig environment options.
     *
     * @link https://twig.symfony.com/doc/2.x/api.html#environment-options
     *
     * @param array $options An array of environment options.
     *
     * @return array
     */
    #[Filter("timber/twig/environment/options")]
    function update_twig_environment_options($options)
    {
        // $options['autoescape'] = true;

        return $options;
    }

    #[Filter("body_class")]
    function add_page_slug_body_class(array $classes): array
    {
        if (is_page()) {
            $slug = get_queried_object()?->post_name;
            if ($slug) {
                $classes[] = "page-" . $slug;
            }
        }
        return $classes;
    }

    public static function get_related_films(
        int $post_id,
        int $limit = 3,
        array $exclude_extra = [],
    ): array {
        $taxonomies = ["genere", "area-tematica", "paese"];

        // Collect taxonomy term IDs for the current film
        $term_ids = [];
        foreach ($taxonomies as $tax) {
            $terms = wp_get_post_terms($post_id, $tax, ["fields" => "ids"]);
            if (!is_wp_error($terms)) {
                $term_ids = array_merge($term_ids, $terms);
            }
        }

        // Collect sezione post IDs from the CPT relationship field
        $sezione_ids = self::extract_post_ids(get_field("sezione", $post_id));

        // Get all published films except the current one and any manual picks
        $exclude = array_merge([$post_id], $exclude_extra);
        $candidates = get_posts([
            "post_type" => "film",
            "post_status" => "publish",
            "posts_per_page" => -1,
            "fields" => "ids",
            "post__not_in" => $exclude,
        ]);

        if (empty($candidates)) {
            return [];
        }

        // Preload sezione meta for all candidates in one query (avoids N+1)
        global $wpdb;
        $placeholders = implode(",", array_fill(0, count($candidates), "%d"));
        $candidate_sezioni = [];
        if (!empty($sezione_ids)) {
            $rows = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT post_id, meta_value FROM {$wpdb->postmeta}
                     WHERE meta_key = 'sezione' AND post_id IN ($placeholders)",
                    ...$candidates,
                ),
            );
            foreach ($rows as $row) {
                $ids = @unserialize($row->meta_value);
                $candidate_sezioni[(int) $row->post_id] = is_array($ids)
                    ? array_map("intval", $ids)
                    : [];
            }
        }

        // Score each candidate: +1 per shared taxonomy term, +2 per shared sezione
        $scores = [];
        foreach ($candidates as $candidate_id) {
            $shared = 0;
            foreach ($taxonomies as $tax) {
                $candidate_terms = wp_get_post_terms($candidate_id, $tax, [
                    "fields" => "ids",
                ]);
                if (!is_wp_error($candidate_terms)) {
                    $shared += count(
                        array_intersect($term_ids, $candidate_terms),
                    );
                }
            }
            if (!empty($sezione_ids)) {
                $c_sezione_ids = $candidate_sezioni[$candidate_id] ?? [];
                $shared +=
                    count(array_intersect($sezione_ids, $c_sezione_ids)) * 2;
            }
            if ($shared > 0) {
                $scores[$candidate_id] = $shared;
            }
        }

        arsort($scores);

        $result = array_slice(array_keys($scores), 0, $limit);

        // Fill up to $limit with random films if not enough scored matches
        if (count($result) < $limit) {
            $exclude = array_merge([$post_id], $exclude_extra, $result);
            $fillers = get_posts([
                "post_type" => "film",
                "post_status" => "publish",
                "posts_per_page" => $limit - count($result),
                "fields" => "ids",
                "post__not_in" => $exclude,
                "orderby" => "rand",
            ]);
            $result = array_merge($result, $fillers);
        }

        return $result;
    }

    /** Normalise an ACF relationship value to a flat array of integer post IDs. */
    private static function extract_post_ids(mixed $value): array
    {
        if (empty($value)) {
            return [];
        }
        return array_map(
            fn($p) => is_object($p) ? (int) $p->ID : (int) $p,
            (array) $value,
        );
    }

    // Redirect non-users to coming soon page, but allow certain other pages
    #[Action("template_redirect")]
    function coming_soon_redirect()
    {
        global $pagenow;

        $is_coming_soon = get_field("enable_coming_soon", "option");

        if (!$is_coming_soon) {
            return;
        }

        $allowed_pages = ["login", "coming-soon"];

        if (
            !is_user_logged_in() &&
            !is_page($allowed_pages) &&
            $pagenow != "wp-login.php"
        ) {
            $coming_soon_page = get_page_by_path("coming-soon");
            if (
                $coming_soon_page &&
                function_exists("pll_get_post") &&
                function_exists("pll_current_language")
            ) {
                $translated_id = pll_get_post(
                    $coming_soon_page->ID,
                    pll_current_language(),
                );
                if ($translated_id) {
                    wp_redirect(get_permalink($translated_id));
                    exit();
                }
            }
            wp_redirect(home_url("coming-soon"));
            exit();
        }
    }

    #[Action("template_redirect")]
    public function redirect_attivita_archives()
    {
        if (is_post_type_archive("attivita-doc")) {
            wp_redirect(get_post_type_archive_link("contents-doc"), 301);
            exit();
        }
        if (is_post_type_archive("attivita-drama")) {
            wp_redirect(get_post_type_archive_link("contents-drama"), 301);
            exit();
        }
    }

    #[Filter("wpseo_opengraph_image")]
    public function fallback_og_image(string $image): string
    {
        if ($image) {
            return $image;
        }

        return get_template_directory_uri() . '/assets/images/share-img/share-img.png';
    }

    #[Action("wpseo_add_opengraph_images")]
    public function fallback_og_image_add(\WPSEO_OpenGraph_Image $image_container): void
    {
        if (!$image_container->has_images()) {
            $image_container->add_image(get_template_directory_uri() . '/assets/images/share-img/share-img.png');
        }
    }
}
