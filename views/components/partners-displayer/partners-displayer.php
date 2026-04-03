<?php

use Extended\ACF\Fields\Link;
use Extended\ACF\Fields\Text;

return [
    Text::make("Titolo", "title"),
    Link::make("Link", "link")->format("array"),
];
