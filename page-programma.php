<?php

/**
 * Template Name: Programma
 */

namespace App;

use Timber\Timber;

$context         = Timber::context();
$context["post"] = Timber::get_post();

Timber::render("templates/page-programma.twig", $context);
