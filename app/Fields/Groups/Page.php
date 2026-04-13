<?php

namespace App\Fields\Groups;

use Extended\ACF\Fields\FlexibleContent;
use Extended\ACF\Fields\Layout;
use Extended\ACF\Location;

register_extended_field_group([
    "title" => "Page",
    "location" => [Location::where("page_template", "=", "default")],
    "fields" => [
        FlexibleContent::make("", "components")
            ->button("Aggiungi sezione")
            ->layouts([
                Layout::make("Text Displayer", "text_displayer")
                    ->layout("block")
                    ->fields(
                        require get_stylesheet_directory() .
                            "/views/components/text-displayer/text-displayer.php",
                    ),
                Layout::make("Quote", "quote")
                    ->layout("block")
                    ->fields(
                        require get_stylesheet_directory() .
                            "/views/components/quote/quote.php",
                    ),
            ])
            ->withSettings([
                "acfe_flexible_advanced" => 1,
                "acfe_flexible_stylised_button" => 1,
                "acfe_flexible_add_actions" => ["toggle", "copy"],
                "acfe_flexible_layouts_state" => "user",
                "acfe_flexible_modal_edit" => [
                    "acfe_flexible_modal_edit_enabled" => "0",
                    "acfe_flexible_modal_edit_size" => "large",
                ],
                "acfe_flexible_modal" => [
                    "acfe_flexible_modal_enabled" => "0",
                ],
            ]),
    ],
    "style" => "",
    "hide_on_screen" => ["the_content"],
    "acfe_seamless_style" => 1,
]);
