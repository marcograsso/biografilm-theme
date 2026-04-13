<?php

/**
 * Template Name: Giurie
 */

namespace App;

use Timber\Timber;

$context         = Timber::context();
$context["post"] = Timber::get_post();

Timber::render("templates/page-giurie.twig", $context);
