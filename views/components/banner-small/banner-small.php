<?php

use Extended\ACF\Fields\Link;
use Extended\ACF\Fields\Text;

return [
    Text::make("Titolo", "title"),
    Text::make("Sottotitolo", "subtitle"),
    Link::make("Link", "link")->format("array"),
];
