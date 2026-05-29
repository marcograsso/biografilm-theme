<?php

use Timber\Timber;

class FWP_Config
{
    public function __construct()
    {
        add_filter(
            "facetwp_facet_html",
            [$this, "render_custom_templates"],
            10,
            2,
        );
        add_filter("facetwp_assets", [$this, "remove_facetwp_assets"]);
    }

    function remove_facetwp_assets($assets)
    {
        unset($assets["front.css"]);
        unset($assets["fSelect.css"]);
        return $assets;
    }

    function render_custom_templates($output, $params)
    {
        $facet_type = $params["facet"]["type"] ?? "";
        $facet_ui_type = $params["facet"]["ui_type"] ?? $facet_type;
        $effective_type = $facet_ui_type ?: $facet_type;

        $context = Timber::context();
        $context["post"] = Timber::get_post();
        $context["params"] = $params;
        $template = "";

        switch ($effective_type) {
            case "fselect":
                $template = "components/filters/select/select.twig";
                break;

            case "search":
                $template = "components/filters/search/search.twig";
                break;

            case "sort":
                $template = "components/filters/sort/sort.twig";
                if (
                    is_post_type_archive("film") &&
                    empty($params["selected_values"])
                ) {
                    $context["params"]["selected_values"] = ["sezione_asc"];
                    $context["params"]["is_default_sort"] = true;
                }
                break;
        }

        if ($template) {
            $output = Timber::compile($template, $context);
        }

        return $output;
    }
}

new FWP_Config();

add_filter("facetwp_facet_dropdown_show_counts", "__return_false");

add_action("pre_get_posts", function ($query) {
    if (
        !$query->is_main_query() ||
        !is_post_type_archive("film") ||
        is_admin()
    ) {
        return;
    }
    $query->set("meta_key", "sezione");
    $query->set("orderby", ["meta_value" => "ASC", "title" => "ASC"]);
});

add_filter(
    "facetwp_query_args",
    function ($query_args, $params) {
        if (($params->template["name"] ?? "") === "films") {
            $query_args = array_merge(
                $query_args,
                include __DIR__ . "/templates/film-query.php",
            );
        }
        return $query_args;
    },
    10,
    2,
);

// Override the admin-configured sezione_asc sort option so FacetWP does not
// add its own INNER JOIN on the sezione meta key — that would conflict with
// the LEFT JOIN in posts_clauses below and exclude films with no sezione.
add_filter(
    "facetwp_sort_options",
    function ($options, $params) {
        $options["sezione_asc"] = [
            "label" => "Sezione (A-Z)",
            "query_args" => [],
        ];
        return $options;
    },
    10,
    2,
);


// Resolve sezione ACF relationship data during FacetWP indexing.
//
// ACF stores relationship fields as a serialised array of post IDs in post_meta
// (meta_key = "sezione", value = 'a:1:{i:0;i:42;}'). When the sezione facet
// data source is set to "Custom field › sezione" in FacetWP, this filter
// intercepts each raw value and converts it to the sezione post slug (value)
// and post title (display value) that the facet UI actually shows.
//
// ⚠ In the FacetWP admin you must:
//   1. Set the "sezione" facet data source to Custom field → _film_sezione
//      (the flat denormalised meta, same pattern as _film_title / _film_regista)
//   2. Make sure both "film" and "proiezione" post types are indexed
//   3. Re-index
//
// The _film_sezione meta holds the plain sezione title, so no serialisation
// magic is needed. The facetwp_index_row filter below is kept as a fallback
// in case the "sezione" ACF field is used as source instead.
add_filter(
    "facetwp_index_row",
    function ($params, $class) {
        if ($params["facet_name"] !== "sezione") {
            return $params;
        }

        $raw = $params["facet_value"];

        // Collect post IDs from a single ID or a serialised ACF array
        $ids = [];
        if (is_numeric($raw) && (int) $raw > 0) {
            $ids = [(int) $raw];
        } elseif (is_string($raw)) {
            $maybe = @unserialize($raw);
            if (is_array($maybe)) {
                $ids = array_map("intval", array_filter($maybe));
            }
        }

        if (empty($ids)) {
            $params["facet_value"] = "";
            return $params;
        }

        // Use the first (and normally only) sezione post
        $post = get_post($ids[0]);
        if ($post && $post->post_type === "sezione") {
            $params["facet_value"] = $post->post_name;
            $params["facet_display_value"] = $post->post_title;
        } else {
            $params["facet_value"] = "";
        }

        return $params;
    },
    10,
    2,
);

// Translate day abbreviations in the "days" radio facet based on current language.
// facet_value is a date string (Y-m-d or Ymd); it_day logic is replicated here.
add_filter(
    "facetwp_facet_display_value",
    function ($label, $params) {
        if (($params["facet"]["name"] ?? "") !== "days") {
            return $label;
        }
        $ts = strtotime($params["row"]["facet_value"] ?? "");
        if (!$ts) {
            return $label;
        }
        $is_en =
            function_exists("pll_current_language") &&
            pll_current_language() === "en";
        $abbr = $is_en
            ? ["SUN", "MON", "TUE", "WED", "THU", "FRI", "SAT"]
            : ["DOM", "LUN", "MAR", "MER", "GIO", "VEN", "SAB"];
        return $abbr[(int) date("w", $ts)] . " " . date("j", $ts);
    },
    10,
    2,
);

// Auto-select the first available day on the programme pages (both Italian
// /programma and English /en/festival/program).
// Order the programma query by COALESCE(orario, orario_inizio) so that
// proiezioni (orario) and eventi-programma (orario_inizio) sort together.
add_filter(
    "posts_clauses",
    function ($clauses, $query) {
        global $wpdb;
        $types = (array) ($query->query_vars["post_type"] ?? []);
        if (
            !in_array("proiezione", $types) ||
            !in_array("eventi-programma", $types)
        ) {
            return $clauses;
        }

        $clauses["join"] .=
            " LEFT JOIN {$wpdb->postmeta} AS _pm_orario" .
            " ON ({$wpdb->posts}.ID = _pm_orario.post_id AND _pm_orario.meta_key = 'orario')" .
            " LEFT JOIN {$wpdb->postmeta} AS _pm_orario_inizio" .
            " ON ({$wpdb->posts}.ID = _pm_orario_inizio.post_id AND _pm_orario_inizio.meta_key = 'orario_inizio')";

        $clauses["orderby"] .=
            ($clauses["orderby"] ? ", " : "") .
            "COALESCE(_pm_orario.meta_value, _pm_orario_inizio.meta_value) ASC";

        return $clauses;
    },
    20,
    2,
);

// Order whos-coming archive alphabetically by company name (azienda → titolo).
add_filter(
    "posts_clauses",
    function ($clauses, $query) {
        global $wpdb;
        $types = (array) ($query->query_vars["post_type"] ?? []);
        if (!in_array("whos-coming", $types)) {
            return $clauses;
        }

        $clauses["join"] .=
            " LEFT JOIN {$wpdb->postmeta} AS _pm_azienda" .
            " ON ({$wpdb->posts}.ID = _pm_azienda.post_id AND _pm_azienda.meta_key = 'whos_coming_azienda_titolo')";

        $clauses[
            "orderby"
        ] = "COALESCE(NULLIF(_pm_azienda.meta_value, ''), 'zzz') ASC, {$wpdb->posts}.post_title ASC";

        return $clauses;
    },
    20,
    2,
);

add_filter("facetwp_preload_url_vars", function ($url_vars) {
    if (is_post_type_archive("film") && empty($url_vars["order_by"])) {
        $url_vars["order_by"] = ["sezione_asc"];
    }
    return $url_vars;
});

add_filter("facetwp_preload_url_vars", function ($url_vars) {
    if (false === strpos(FWP()->helper->get_uri(), "program")) {
        return $url_vars;
    }

    if (!empty($url_vars["days"])) {
        return $url_vars;
    }

    $posts = get_posts([
        "post_type" => ["proiezione", "eventi-programma"],
        "post_status" => "publish",
        "posts_per_page" => 1,
        "meta_key" => "data",
        "orderby" => "meta_value",
        "order" => "ASC",
        "fields" => "ids",
    ]);

    if (!empty($posts)) {
        $raw = get_post_meta($posts[0], "data", true);
        if ($raw) {
            $date =
                DateTime::createFromFormat("Ymd", $raw) ?:
                DateTime::createFromFormat("Y-m-d", $raw);
            if ($date) {
                $url_vars["days"] = [$date->format("Y-m-d")];
            }
        }
    }

    return $url_vars;
});
