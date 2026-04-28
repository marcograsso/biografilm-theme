<?php

namespace App\Taxonomies;

class EventiTaxonomies
{
    public static function register(): void
    {
        $taxonomies = [
            [
                "slug"     => "tipologia-evento",
                "singular" => "Tipologia",
                "plural"   => "Tipologie",
            ],
            [
                "slug"     => "luogo-evento",
                "singular" => "Luogo",
                "plural"   => "Luoghi",
            ],
        ];

        foreach ($taxonomies as $tax) {
            register_extended_taxonomy(
                $tax["slug"],
                ["evento"],
                [
                    "hierarchical" => false,
                    "labels"       => [
                        "name"          => $tax["plural"],
                        "singular_name" => $tax["singular"],
                        "add_new_item"  => "Aggiungi " . $tax["singular"],
                        "edit_item"     => "Modifica " . $tax["singular"],
                        "search_items"  => "Cerca " . $tax["plural"],
                        "not_found"     => "Nessun risultato trovato",
                        "all_items"     => "Tutti " . strtolower($tax["plural"]),
                    ],
                ],
                [
                    "singular" => $tax["singular"],
                    "plural"   => $tax["plural"],
                    "slug"     => $tax["slug"],
                ],
            );
        }
    }
}
