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

add_filter('facetwp_preload_url_vars', function ($url_vars) {
    if (false === strpos(FWP()->helper->get_uri(), 'programma')) {
        return $url_vars;
    }

    if (!empty($url_vars['days'])) {
        return $url_vars;
    }

    $posts = get_posts([
        'post_type'      => 'proiezione',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'meta_key'       => 'data',
        'orderby'        => 'meta_value',
        'order'          => 'ASC',
        'fields'         => 'ids',
    ]);

    if (!empty($posts)) {
        $raw = get_post_meta($posts[0], 'data', true);
        if ($raw) {
            $date = DateTime::createFromFormat('Ymd', $raw)
                ?: DateTime::createFromFormat('Y-m-d', $raw);
            if ($date) {
                $url_vars['days'] = [$date->format('Y-m-d')];
            }
        }
    }

    return $url_vars;
});
