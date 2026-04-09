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
        PostTypes\Film::register();
        PostTypes\Proiezione::register();
        PostTypes\News::register();
    }

    #[Action("init")]
    public function register_taxonomies()
    {
        Taxonomies\FilmTaxonomies::register();
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

        $context["current_url"] = URLHelper::get_current_url();
        $context["header_show_date_location"] = get_field("header_show_date_location", "option");
        $context["header_date"] = get_field("header_date", "option") ?: "5 — 15.06.2026";
        $context["header_location"] = get_field("header_location", "option") ?: "Bologna";
        $context["footer_image"] = get_field("footer_image", "option");
        $context["footer_image_mobile"] = get_field("footer_image_mobile", "option");
        $context["environment"] = $this->vite->environment;

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
        }
        $context["current_section"] = $section;

        // Build breadcrumbs from page hierarchy
        $breadcrumbs = [];
        if (is_post_type_archive('film')) {
            $breadcrumbs[] = ["url" => home_url("/"), "title" => "Festival"];
            $breadcrumbs[] = ["url" => "", "title" => "Tutti i film"];
        } elseif ($post && !is_front_page()) {
            if (get_post_type($post->ID) === 'film') {
                $breadcrumbs[] = ["url" => get_post_type_archive_link('film'), "title" => "Tutti i film"];
                $breadcrumbs[] = ["url" => "", "title" => get_the_title($post->ID)];
            } else {
                $ancestors = get_post_ancestors($post->ID);
                $breadcrumbs[] = ["url" => home_url("/"), "title" => empty($ancestors) ? "Biografilm" : "Festival"];
                $front_page_id = (int) get_option('page_on_front');
                foreach (array_reverse($ancestors) as $ancestor_id) {
                    if ($ancestor_id === $front_page_id) {
                        continue;
                    }
                    $breadcrumbs[] = [
                        "url"   => get_permalink($ancestor_id),
                        "title" => get_the_title($ancestor_id),
                    ];
                }
                $breadcrumbs[] = ["url" => "", "title" => get_the_title($post->ID)];
            }
        }
        $context["breadcrumbs"] = $breadcrumbs;

        if (is_singular('film') && $post) {
            $manual = get_field('film_correlati', $post->ID);
            $ids = !empty($manual)
                ? array_map(fn($p) => is_object($p) ? $p->ID : (int) $p, $manual)
                : [];
            if (count($ids) < 3) {
                $ids = array_merge($ids, self::get_related_films($post->ID, 3 - count($ids), $ids));
            }
            $context['related_films'] = array_map(fn($id) => Timber::get_post($id), $ids);
        }

        $industry_page = get_page_by_path("industry");
        $campus_page = get_page_by_path("campus");
        $context["nav_urls"] = [
            "festival" => home_url("/"),
            "industry" => $industry_page ? get_permalink($industry_page->ID) : home_url("/industry/"),
            "campus"   => $campus_page ? get_permalink($campus_page->ID) : home_url("/campus/"),
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
        $twig->addFunction(
            new \Twig\TwigFunction("ray", function (...$params) {
                ray(...$params);
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
                $classes[] = 'page-' . $slug;
            }
        }
        return $classes;
    }

    public static function get_related_films(int $post_id, int $limit = 3, array $exclude_extra = []): array
    {
        $taxonomies = ['sezione', 'genere', 'area-tematica', 'paese'];

        // Collect all term IDs for this film across relevant taxonomies
        $term_ids = [];
        foreach ($taxonomies as $tax) {
            $terms = wp_get_post_terms($post_id, $tax, ['fields' => 'ids']);
            if (!is_wp_error($terms)) {
                $term_ids = array_merge($term_ids, $terms);
            }
        }

        // Get all published films except the current one and any manual picks
        $candidates = !empty($term_ids) ? get_posts([
            'post_type'      => 'film',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'post__not_in'   => array_merge([$post_id], $exclude_extra),
        ]) : [];

        // Score each candidate by number of shared terms
        $scores = [];
        foreach ($candidates as $candidate_id) {
            $shared = 0;
            foreach ($taxonomies as $tax) {
                $candidate_terms = wp_get_post_terms($candidate_id, $tax, ['fields' => 'ids']);
                if (!is_wp_error($candidate_terms)) {
                    $shared += count(array_intersect($term_ids, $candidate_terms));
                }
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
                'post_type'      => 'film',
                'post_status'    => 'publish',
                'posts_per_page' => $limit - count($result),
                'fields'         => 'ids',
                'post__not_in'   => $exclude,
                'orderby'        => 'rand',
            ]);
            $result = array_merge($result, $fillers);
        }

        return $result;
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
            wp_redirect(home_url("coming-soon"));
            exit();
        }
    }
}
