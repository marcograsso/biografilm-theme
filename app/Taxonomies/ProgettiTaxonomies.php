<?php

namespace App\Taxonomies;

class ProgettiTaxonomies
{
    public static function register(): void
    {
        $taxonomies = [
            [
                "slug"     => "tipologia-progetto",
                "singular" => "Tipologia",
                "plural"   => "Tipologie",
            ],
            [
                "slug"     => "target-progetto",
                "singular" => "Target",
                "plural"   => "Target",
            ],
            [
                "slug"     => "status-progetto",
                "singular" => "Status",
                "plural"   => "Status",
            ],
            [
                "slug"     => "luogo-progetto",
                "singular" => "Luogo",
                "plural"   => "Luoghi",
            ],
        ];

        foreach ($taxonomies as $tax) {
            register_extended_taxonomy(
                $tax["slug"],
                ["progetto"],
                [
                    "hierarchical" => false,
                    "labels" => [
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
