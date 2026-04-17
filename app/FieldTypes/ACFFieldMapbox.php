<?php

namespace App\FieldTypes;

use Timber\Timber;

if (!defined("ABSPATH")) {
    exit();
}

class ACFFieldMapbox extends \acf_field
{
    public $show_in_rest = true;

    private $env;

    public function __construct()
    {
        $this->name = "mapbox";
        $this->label = __("Mapbox Address", "biografilm");
        $this->category = "content";
        $this->description = __(
            "Address input with Mapbox geocoding and map preview",
            "biografilm",
        );
        $this->doc_url = "https://docs.mapbox.com/";

        $this->defaults = [
            "mapbox_api_key" => "",
            "default_country" => "it",
        ];

        $this->l10n = [
            "no_api_key" => __(
                "Mapbox API key is required. Please add it in field settings.",
                "biografilm",
            ),
            "enter_address" => __("Inserisci un indirizzo…", "biografilm"),
        ];

        $this->env = [
            "url" => site_url(str_replace(ABSPATH, "", __DIR__)),
            "version" => "1.0",
        ];

        parent::__construct();
    }

    public function render_field_settings($field)
    {
        acf_render_field_setting($field, [
            "label" => __("Mapbox API Key", "biografilm"),
            "instructions" => __(
                "Enter your Mapbox public access token. Get one at https://account.mapbox.com/",
                "biografilm",
            ),
            "type" => "text",
            "name" => "mapbox_api_key",
            "required" => true,
        ]);

        acf_render_field_setting($field, [
            "label" => __("Default Country", "biografilm"),
            "instructions" => __(
                "Limit address search to specific country (e.g., it, us, gb)",
                "biografilm",
            ),
            "type" => "text",
            "name" => "default_country",
            "placeholder" => "it",
        ]);
    }

    public function render_field($field)
    {
        $value = wp_parse_args($field["value"], [
            "address"   => "",
            "latitude"  => "",
            "longitude" => "",
            "city"      => "",
            "province"  => "",
            "cap"       => "",
        ]);

        $mapbox_api_key  = $field["mapbox_api_key"] ?? "";
        $default_country = $field["default_country"] ?? "it";

        echo Timber::compile("admin/acf-mapbox.twig", [
            "mapbox_api_key"  => $mapbox_api_key,
            "default_country" => $default_country,
            "field"           => $field,
            "value"           => $value,
            "l10n"            => $this->l10n,
        ]);
    }

    public function input_admin_enqueue_scripts()
    {
        wp_enqueue_script(
            "mapbox-search-js",
            "https://api.mapbox.com/search-js/v1.5.0/web.js",
            [],
            "1.5.0",
            false,
        );
    }

    public function validate_value($valid, $value, $field, $input)
    {
        return $valid;
    }

    public function format_value($value, $post_id, $field)
    {
        if (empty($value)) {
            return [
                "address"   => "",
                "latitude"  => "",
                "longitude" => "",
                "city"      => "",
                "province"  => "",
                "cap"       => "",
            ];
        }

        return wp_parse_args($value, [
            "address"   => "",
            "latitude"  => "",
            "longitude" => "",
            "city"      => "",
            "province"  => "",
            "cap"       => "",
        ]);
    }
}
