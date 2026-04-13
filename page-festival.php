<?php

/**
 * Template Name: Festival
 */

namespace App;

use Timber\Timber;

$context         = Timber::context();
$context["post"] = Timber::get_post();

Timber::render("templates/page-festival.twig", $context);
