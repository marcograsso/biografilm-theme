<?php

/**
 * Template Name: Locations
 */

namespace App;

use Timber\Timber;

$context         = Timber::context();
$context["post"] = Timber::get_post();

Timber::render("templates/page-locations.twig", $context);
