<?php

use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Textarea;

return [
    Tab::make("Generali"),
    Textarea::make("Descrizione breve", "descrizione_breve")
        ->rows(4)
        ->helperText(
            "Questo testo verrà utilizzato come anteprima nelle card del progetto.",
        ),
    Tab::make("Tabs"),
];
