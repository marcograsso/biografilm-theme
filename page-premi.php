<?php

/**
 * Template Name: Premi
 */

namespace App;

use Timber\Timber;

$context         = Timber::context();
$context["post"] = Timber::get_post();

Timber::render("templates/page-premi.twig", $context);
