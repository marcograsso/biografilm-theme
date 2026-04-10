<?php

namespace App\Taxonomies;

class FilmTaxonomies
{
    public static function register(): void
    {
        $taxonomies = [
            [
                "slug"     => "paese",
                "singular" => "Paese",
                "plural"   => "Paesi",
            ],
            [
                "slug"     => "genere",
                "singular" => "Genere",
                "plural"   => "Generi",
            ],
            [
                "slug"     => "area-tematica",
                "singular" => "Area tematica",
                "plural"   => "Aree tematiche",
            ],
            [
                "slug"     => "badge",
                "singular" => "Badge",
                "plural"   => "Badges",
            ],
        ];

        register_extended_taxonomy(
            "location",
            ["proiezione"],
            [
                "hierarchical" => false,
                "labels" => [
                    "name"          => "Location",
                    "singular_name" => "Location",
                    "add_new_item"  => "Aggiungi location",
                    "edit_item"     => "Modifica location",
                    "search_items"  => "Cerca location",
                    "not_found"     => "Nessuna location trovata",
                    "all_items"     => "Tutte le location",
                ],
            ],
            [
                "singular" => "Location",
                "plural"   => "Location",
                "slug"     => "location",
            ],
        );

        foreach ($taxonomies as $tax) {
            register_extended_taxonomy(
                $tax["slug"],
                ["film", "proiezione"],
                [
                    "hierarchical" => false,
                    "labels" => [
                        "name"          => $tax["plural"],
                        "singular_name" => $tax["singular"],
                        "add_new_item"  => "Aggiungi " . $tax["singular"],
                        "edit_item"     => "Modifica " . $tax["singular"],
                        "search_items"  => "Cerca " . $tax["plural"],
                        "not_found"     => "Nessun risultato trovato",
                        "all_items"     => "Tutt* " . strtolower($tax["plural"]),
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
