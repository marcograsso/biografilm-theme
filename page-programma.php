<?php

/**
 * Template Name: Programma
 */

namespace App;

use Timber\Timber;

$context         = Timber::context();
$context["post"] = Timber::get_post();

$tipo = get_field("tipo_programma") ?: "festival";
$context["tipo"] = $tipo;

if ($tipo === "doc" || $tipo === "drama") {
    $post_type = $tipo === "doc" ? "contents-doc" : "contents-drama";
    $context["posts"] = Timber::get_posts([
        "post_type"      => $post_type,
        "post_status"    => "publish",
        "posts_per_page" => -1,
        "orderby"        => "title",
        "order"          => "ASC",
    ]);
}

Timber::render("templates/page-programma.twig", $context);
