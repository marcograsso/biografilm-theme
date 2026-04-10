<?php

use Extended\ACF\ConditionalLogic;
use Extended\ACF\Fields\ButtonGroup;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\TrueFalse;

return [
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
];
