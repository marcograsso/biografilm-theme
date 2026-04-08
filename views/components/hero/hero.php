<?php

use Extended\ACF\ConditionalLogic;
use Extended\ACF\Fields\ButtonGroup;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\TrueFalse;
use Extended\ACF\Location;

register_extended_field_group([
    "title" => "Hero",
    "location" => [Location::where("post_type", "=", "page")],
    "fields" => [
        TrueFalse::make("Nascondi hero", "hide_hero")
            ->stylized("Sì", "No")
            ->default(false),
        Text::make("Titolo alternativo hero", "hero_alt_title")
            ->conditionalLogic([
                ConditionalLogic::where("hide_hero", "==", "0"),
            ]),
        ButtonGroup::make("Stile titolo hero", "hero_title_style")
            ->choices(["primary" => "Primary", "secondary" => "Secondary", "tertiary" => "Tertiary"])
            ->default("primary")
            ->conditionalLogic([
                ConditionalLogic::where("hide_hero", "==", "0"),
            ]),
    ],
    "position" => "acf_after_title",
    "style" => "default",
]);
