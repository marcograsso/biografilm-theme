<?php

declare(strict_types=1);

namespace App\WordPress;

use App\IsPluginActive;
use Timber\ImageHelper;
use Yard\Hook\Action;
use Yard\Hook\Filter;

class WordPress
{
    public function __construct()
    {
        // Remove WordPress version.
        remove_action("wp_head", "wp_generator");

        // Remove RSS feed links.
        remove_action("wp_head", "feed_links", 2);
        remove_action("wp_head", "feed_links_extra", 3);

        // Remove Really Simple Discovery link.
        remove_action("wp_head", "rsd_link");

        // Remove REST API link tag from <head>.
        remove_action("wp_head", "rest_output_link_wp_head", 10);

        // Remove REST API link tag from HTML headers.
        remove_action("template_redirect", "rest_output_link_header", 11);

        // Remove emojis.
        // WordPress 6.4 deprecated the use of print_emoji_styles() function, but it has
        // been retained for backward compatibility purposes.
        // https://make.wordpress.org/core/2023/10/17/replacing-hard-coded-style-tags-with-wp_add_inline_style/
        remove_action("wp_head", "print_emoji_detection_script", 7);
        remove_action("admin_print_scripts", "print_emoji_detection_script");
        remove_action("wp_print_styles", "print_emoji_styles");
        remove_action("admin_print_styles", "print_emoji_styles");
        remove_filter("the_content_feed", "wp_staticize_emoji");
        remove_filter("comment_text_rss", "wp_staticize_emoji");
        remove_filter("wp_mail", "wp_staticize_emoji_for_email");

        // Remove oEmbeds.
        remove_action("wp_head", "wp_oembed_add_discovery_links", 10);
        remove_action("wp_head", "wp_oembed_add_host_js");

        // Remove password change notification.
        remove_action(
            "after_password_reset",
            "wp_password_change_notification",
        );
    }

    // Disable both post and comment feeds
    #[Action("do_feed")]
    #[Action("do_feed_rdf")]
    #[Action("do_feed_rss")]
    #[Action("do_feed_rss2")]
    #[Action("do_feed_atom")]
    #[Action("do_feed_rss2_comments")]
    #[Action("do_feed_atom_comments")]
    public function disable_feeds()
    {
        wp_redirect(home_url());
        exit();
    }

    #[Filter("xmlrpc_enabled")]
    #[Filter("xmlrpc_methods")]
    public function disable_xml_rpc()
    {
        return false;
    }

    // Disable default users API endpoints for security.
    // https://www.wp-tweaks.com/hackers-can-find-your-wordpress-username/
    #[Filter("rest_endpoints")]
    function disable_rest_endpoints(array $endpoints): array
    {
        if (!is_user_logged_in()) {
            if (isset($endpoints["/wp/v2/users"])) {
                unset($endpoints["/wp/v2/users"]);
            }

            if (isset($endpoints["/wp/v2/users/(?P<id>[\d]+)"])) {
                unset($endpoints["/wp/v2/users/(?P<id>[\d]+)"]);
            }
        }

        return $endpoints;
    }

    #[Filter("jpeg_quality", 10, 2)]
    public function remove_jpeg_compression(): int
    {
        return 100;
    }

    #[Filter("upload_mimes")]
    public function allow_svg_upload(array $mimes): array
    {
        if (current_user_can("administrator")) {
            $mimes["svg"] = "image/svg+xml";
            $mimes["svgz"] = "image/svg+xml";
        }

        return $mimes;
    }

    #[Filter("wp_check_filetype_and_ext", 10, 4)]
    public function fix_svg_filetype(array $data, string $file, string $filename, ?array $mimes): array
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if ($ext === "svg" || $ext === "svgz") {
            $data["ext"] = $ext;
            $data["type"] = "image/svg+xml";
        }

        return $data;
    }

    private function is_svg_upload(array $file): bool
    {
        $ext = strtolower(pathinfo($file["name"] ?? "", PATHINFO_EXTENSION));
        $type = $file["type"] ?? "";

        return $ext === "svg" || $ext === "svgz" || $type === "image/svg+xml";
    }

    #[Filter("file_is_displayable_image", 10, 2)]
    public function svg_is_displayable_image(bool $result, string $path): bool
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if ($ext === "svg" || $ext === "svgz") {
            return true;
        }

        return $result;
    }

    #[Filter("wp_handle_upload_prefilter")]
    public function svg_handle_upload_prefilter(array $file): array
    {
        error_log("SVG DEBUG prefilter: name={$file['name']} type={$file['type']} is_svg=" . ($this->is_svg_upload($file) ? 'yes' : 'no'));

        if ($this->is_svg_upload($file)) {
            add_filter("wp_image_editors", "__return_empty_array");
            error_log("SVG DEBUG: image editors disabled");
        }

        return $file;
    }

    #[Filter("intermediate_image_sizes_advanced", 10, 2)]
    public function skip_svg_image_sizes(array $sizes, array $metadata): array
    {
        error_log("SVG DEBUG intermediate_image_sizes_advanced: file=" . ($metadata["file"] ?? "none"));

        if (
            isset($metadata["file"]) &&
            str_ends_with(strtolower($metadata["file"]), ".svg")
        ) {
            error_log("SVG DEBUG: skipping image sizes for SVG");
            return [];
        }

        return $sizes;
    }

    #[Filter("wp_generate_attachment_metadata", 10, 2)]
    public function skip_svg_metadata(array $metadata, int $attachment_id): array
    {
        $file = get_attached_file($attachment_id);
        $ext = strtolower(pathinfo((string) $file, PATHINFO_EXTENSION));

        error_log("SVG DEBUG generate_metadata: id=$attachment_id file=$file ext=$ext");

        if ($ext === "svg" || $ext === "svgz") {
            error_log("SVG DEBUG: returning minimal metadata for SVG");
            return ["file" => $file];
        }

        return $metadata;
    }

    // Remove Gutenberg's front-end block styles.
    #[Action("wp_enqueue_scripts")]
    public function remove_block_styles(): void
    {
        wp_deregister_style("wp-block-library");
        wp_deregister_style("wp-block-library-theme");
    }

    // Remove core block styles.
    // https://github.com/WordPress/gutenberg/issues/56065
    #[Action("wp_footer")]
    public function remove_core_block_styles(): void
    {
        wp_dequeue_style("core-block-supports");
    }

    // Remove Gutenberg's global styles.
    // https://github.com/WordPress/gutenberg/issues/36834
    #[Action("init")]
    public function remove_global_styles(): void
    {
        // wp_dequeue_style("global-styles") does not work anymore
        remove_action("wp_enqueue_scripts", "wp_enqueue_global_styles");
        remove_action("wp_footer", "wp_enqueue_global_styles", 1);
    }

    // Remove classic theme styles.
    // https://github.com/WordPress/WordPress/commit/143fd4c1f71fe7d5f6bd7b64c491d9644d861355
    #[Action("wp_enqueue_scripts")]
    public function remove_classic_theme_styles(): void
    {
        wp_dequeue_style("classic-theme-styles");
    }

    // Remove auto-sizes contain inline styles.
    // https://make.wordpress.org/core/2024/10/18/auto-sizes-for-lazy-loaded-images-in-wordpress-6-7/
    #[Action("wp_enqueue_scripts")]
    public function remove_auto_sizes_styles(): void
    {
        wp_dequeue_style("wp-img-auto-sizes-contain");
    }

    // Remove the SVG Filters that are mostly if not only used in Full Site Editing/Gutenberg
    // Detailed discussion at: https://github.com/WordPress/gutenberg/issues/36834
    #[Action("init")]
    public function remove_svg_filters(): void
    {
        remove_action(
            "wp_body_open",
            "gutenberg_global_styles_render_svg_filters",
        );
        remove_action("wp_body_open", "wp_global_styles_render_svg_filters");
    }

    // Disable attachment template loading and redirect to 404.
    // WordPress 6.4 introduced an update to disable attachment pages, but this
    // implementation is not as robust as the current one.
    // https://github.com/joppuyo/disable-media-pages/issues/41
    // https://make.wordpress.org/core/2023/10/16/changes-to-attachment-pages/
    #[Filter("template_redirect")]
    public function attachment_redirect_not_found(): void
    {
        if (is_attachment()) {
            global $wp_query;
            $wp_query->set_404();
            status_header(404);
            nocache_headers();
        }
    }

    // Disable attachment canonical redirect links.
    #[Filter("redirect_canonical", 10, 2)]
    public function disable_attachment_canonical_redirect_url(
        string $redirect_url,
        string $requested_url,
    ): string|false {
        if (is_attachment()) {
            return false;
        }

        return $redirect_url;
    }

    // Disable attachment links.
    #[Filter("attachment_link", 10, 2)]
    public function disable_attachment_link(string $url, int $id): string
    {
        if ($attachment_url = wp_get_attachment_url($id)) {
            return $attachment_url;
        }

        return $url;
    }

    // Randomize attachment slugs using UUIDs to avoid slug reservation.
    #[Filter("wp_unique_post_slug", 10, 4)]
    public function disable_attachment_slug_reservation(
        string $slug,
        string $id,
        string $status,
        string $type,
    ): string {
        if ($type !== "attachment") {
            return $slug;
        }

        if (
            preg_match(
                '/^[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}$/iD',
                $slug,
            ) > 0
        ) {
            return $slug;
        }

        return sprintf(
            "%04x%04x-%04x-%04x-%04x-%04x%04x%04x",
            random_int(0, 0xffff),
            random_int(0, 0xffff),
            random_int(0, 0xffff),
            random_int(0, 0x0fff) | 0x4000,
            random_int(0, 0x3fff) | 0x8000,
            random_int(0, 0xffff),
            random_int(0, 0xffff),
            random_int(0, 0xffff),
        );
    }

    // Discourage search engines from indexing in non-production environments.
    #[Action("pre_option_blog_public")]
    public function disable_indexing(): int
    {
        return wp_get_environment_type() === "production" ? 1 : 0;
    }

    // Disable the font library.
    // https://developer.wordpress.org/news/snippets/how-to-disable-the-font-library/
    #[Filter("block_editor_settings_all")]
    public function disable_font_library(array $settings): array
    {
        $settings["fontLibraryEnabled"] = false;

        return $settings;
    }

    // Fixes site previews when shared on Discord
    #[Filter("oembed_response_data")]
    public function disable_embeds_filter_oembed_response_data(
        array $data,
    ): array {
        unset($data["author_url"]);
        unset($data["author_name"]);

        return $data;
    }

    #[Action("admin_head")]
    public function remove_help_tabs(): void
    {
        $screen = get_current_screen();
        if ($screen) {
            $screen->remove_help_tabs();
        }
    }
}
