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
        }

        if ($template) {
            $output = Timber::compile($template, $context);
        }

        return $output;
    }
}

new FWP_Config();

add_filter("facetwp_facet_dropdown_show_counts", "__return_false");

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

add_filter("facetwp_preload_url_vars", function ($url_vars) {
    if (false === strpos(FWP()->helper->get_uri(), "programma")) {
        return $url_vars;
    }

    if (!empty($url_vars["days"])) {
        return $url_vars;
    }

    $posts = get_posts([
        "post_type" => "proiezione",
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
