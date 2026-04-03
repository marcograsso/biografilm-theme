<?php

use Extended\ACF\Fields\PostObject;

return [
    PostObject::make("Contenuto in evidenza", "post")
        ->postTypes(["film", "proiezione"])
        ->format("object"),
];
