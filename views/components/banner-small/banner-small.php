<?php

use Extended\ACF\Fields\Link;
use Extended\ACF\Fields\Select;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\TrueFalse;

return [
    Tab::make("Testi"),
    Text::make("Titolo", "title"),
    Text::make("Sottotitolo", "subtitle"),
    Link::make("Link", "link")->format("array"),

    Tab::make("Stile"),
    Select::make("Padding", "padding")
        ->choices([
            "small" => "Small",
            "large" => "Large",
        ])
        ->default("large"),
    TrueFalse::make("Decorazione Industry", "industry_decoration")
        ->stylized()
        ->default(false),
];
