<?php

namespace App;

use Extended\ACF\Fields\Image;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\Textarea;
use Extended\ACF\Fields\Group;
use Extended\ACF\Fields\WYSIWYGEditor;
use Extended\ACF\Fields\Repeater;
use Extended\ACF\Fields\Link;
use Extended\ACF\Location;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Password;
use Extended\ACF\Fields\TrueFalse;
use Extended\ACF\Fields\URL;

use Yard\Hook\Action;

class ThemeOptions
{
    public function __construct()
    {
        $this->register_options_page();
        $this->register_fields();
    }

    private function register_options_page()
    {
        acf_add_options_page([
            "icon_url" =>
                "data:image/svg+xml;base64," .
                base64_encode(
                    file_get_contents(
                        get_template_directory() .
                            "/assets/images/admin-icon.svg",
                    ),
                ),
            "menu_slug" => "theme-options",
            "page_title" => get_bloginfo("name"),
            "position" => 2.1,
        ]);

        acf_add_options_sub_page([
            "parent_slug" => "theme-options",
            "menu_slug" => "theme-archivi",
            "menu_title" => "Impostazioni",
            "page_title" => "Impostazioni",
        ]);
    }

    private function register_fields()
    {
        register_extended_field_group([
            "title" => "Globals",
            "fields" => [
                TrueFalse::make(
                    "Enable \"Coming Soon\" mode",
                    "enable_coming_soon",
                )
                    ->default(false)
                    ->helperText(
                        "Enable this to show a \"Coming Soon\" mode on the website to everyone except for logged in admins.",
                    ),
                Tab::make("Mapbox"),
                Password::make("Mapbox API Key", "mapbox_api_key")->helperText(
                    "Public access token from account.mapbox.com",
                ),
                Tab::make("Header"),
                TrueFalse::make(
                    "Mostra data e luogo",
                    "header_show_date_location",
                )->default(true),
                Text::make("Data", "header_date")->default("5 — 15.06.2026"),
                Text::make("Luogo", "header_location")->default("Bologna"),
                Link::make("Link Festival", "header_link_festival")->format("array"),
                Link::make("Link Industry", "header_link_industry")->format("array"),
                Link::make("Link Campus", "header_link_campus")->format("array"),
                Tab::make("Social"),
                URL::make("Instagram", "social_instagram"),
                URL::make("Facebook", "social_facebook"),
                URL::make("YouTube", "social_youtube"),
                URL::make("TikTok", "social_tiktok"),
                URL::make("X", "social_x"),
                URL::make("LinkedIn", "social_linkedin"),
                URL::make("Telegram", "social_telegram"),
                Tab::make("Social Industry"),
                URL::make("Instagram", "social_industry_instagram"),
                URL::make("Facebook", "social_industry_facebook"),
                URL::make("YouTube", "social_industry_youtube"),
                URL::make("TikTok", "social_industry_tiktok"),
                URL::make("X", "social_industry_x"),
                URL::make("LinkedIn", "social_industry_linkedin"),
                URL::make("Telegram", "social_industry_telegram"),
                Tab::make("Newsletter"),
                Text::make("Titolo newsletter", "newsletter_title")->default("Vuoi restare aggiornato?"),
                Text::make("Testo social media", "newsletter_social_text")->default("Segui Biografilm sui social"),
                Text::make("Testo social media industry", "newsletter_social_text_industry")->default("Segui Biografilm Industry sui social"),
                Text::make("Shortcode form", "newsletter_form_shortcode")->default('[gravityform id="3" title="false"]'),
                Tab::make("Progetti CTA"),
                Text::make("Titolo", "progetti_cta_titolo")->helperText(
                    "Titolo della CTA. Mostrato solo nelle pagine singolo Progetto, non nell'archivio.",
                ),
                Textarea::make("Descrizione", "progetti_cta_descrizione")->helperText(
                    "Testo della CTA. Mostrato solo nelle pagine singolo Progetto, non nell'archivio.",
                ),
                Link::make("Link", "progetti_cta_link")->helperText(
                    "Link della CTA. Mostrato solo nelle pagine singolo Progetto, non nell'archivio.",
                )->format("array"),
                Tab::make("Footer"),
                Image::make("Immagine footer", "footer_image")->format("array"),
                Image::make(
                    "Immagine footer mobile",
                    "footer_image_mobile",
                )->format("array"),
                WYSIWYGEditor::make("Ragione sociale", "ragione_sociale")
                    ->disableMediaUpload()
                    ->tabs("all")
                    ->toolbar("basic"),
                Repeater::make("Link in basso", "footer_bottom_links")
                    ->fields([
                        Link::make("Link", "link"),
                    ]),
                WYSIWYGEditor::make("Crediti", "footer_credits")
                    ->disableMediaUpload()
                    ->tabs("all")
                    ->toolbar("basic"),
            ],
            "style" => "",
            "menu_order" => 0,
            "location" => [Location::where("options_page", "theme-archivi")],
        ]);
    }
}
