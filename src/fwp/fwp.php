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
