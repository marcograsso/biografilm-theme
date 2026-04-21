<?php

namespace App;

use Timber\Timber;

$context         = Timber::context();
$timber_post     = Timber::get_post();
$context["post"] = $timber_post;

Timber::render(
    [
        "templates/page-" . $timber_post->ID . ".twig",
        "templates/page-" . $timber_post->slug . ".twig",
        "templates/page.twig",
    ],
    $context,
);
