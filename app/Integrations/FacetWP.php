<?php

declare(strict_types=1);

namespace App\Integrations;

use App\IsPluginActive;

#[IsPluginActive("facetwp/index.php")]
class FacetWP
{
    public function __construct()
    {
        // facetwp_refresh fires via the REST API endpoint (facetwp/v1/refresh),
        // before FacetWP renders its template, so pll__() gets the right language.
        add_action('facetwp_refresh', [$this, 'set_polylang_language'], 0);
        add_filter('facetwp_template_html', [$this, 'no_results_html'], 10, 2);
    }

    /**
     * Set Polylang's current language during FacetWP AJAX so that pll__()
     * in Timber templates returns the correct translation.
     * The language is injected into FWP_HTTP.lang by the facetwp-polylang plugin
     * and sent by FacetWP as http_params in the AJAX payload.
     */
    public function no_results_html(string $output, object $class): string
    {
        if ($class->query->found_posts < 1) {
            $output = '<p class="fwp-no-results">No results found</p>';
        }
        return $output;
    }

    public function set_polylang_language(): void
    {
        $raw = $_POST['data'] ?? null;
        if (!$raw) {
            return;
        }

        $data = is_array($raw) ? $raw : json_decode(stripslashes($raw), true);
        $lang = sanitize_key($data['http_params']['lang'] ?? '');

        if (!$lang || !function_exists('PLL') || !PLL() || !isset(PLL()->model)) {
            return;
        }

        $language = PLL()->model->get_language($lang);
        if ($language) {
            PLL()->curlang = $language;
            switch_to_locale($language->locale);
        }
    }
}
