<?php

use Extended\ACF\Fields\Number;
use Extended\ACF\Fields\TrueFalse;

return [
    Number::make("Desktop (rem)", "gap")
        ->min(0)
        ->step(0.5)
        ->default(4)
        ->helperText("xl+")
        ->column(33),
    Number::make("Tablet (rem)", "gap_tablet")
        ->min(0)
        ->step(0.5)
        ->helperText("md–xl, default: desktop")
        ->column(33),
    Number::make("Mobile (rem)", "gap_mobile")
        ->min(0)
        ->step(0.5)
        ->helperText("< md, default: tablet")
        ->column(33),
    TrueFalse::make("Bordo superiore", "border_top")
        ->stylized()
        ->column(50),
    TrueFalse::make("Bordo inferiore", "border_bottom")
        ->stylized()
        ->column(50),
];
