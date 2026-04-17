<?php

namespace App;

use Extended\ACF\Fields\Image;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\Textarea;
use Extended\ACF\Fields\Group;
use Extended\ACF\Fields\WYSIWYGEditor;
use Extended\ACF\Location;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Password;
use Extended\ACF\Fields\TrueFalse;

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
                Password::make("Mapbox API Key", "mapbox_api_key")
                    ->helperText("Public access token from account.mapbox.com"),
                Tab::make("Header"),
                TrueFalse::make("Mostra data e luogo", "header_show_date_location")
                    ->default(true),
                Text::make("Data", "header_date")
                    ->default("5 — 15.06.2026"),
                Text::make("Luogo", "header_location")
                    ->default("Bologna"),
                Tab::make("Footer"),
                Image::make("Immagine footer", "footer_image")
                    ->format("array"),
                Image::make("Immagine footer mobile", "footer_image_mobile")
                    ->format("array"),
            ],
            "style" => "",
            "location" => [Location::where("options_page", "theme-options")],
        ]);
    }
}
