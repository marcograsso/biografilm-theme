<?php

namespace App\Taxonomies;

class WhosComingTaxonomies
{
    public static function register(): void
    {
        $taxonomies = [
            [
                "slug"     => "accredito-whos-coming",
                "singular" => "Accredito",
                "plural"   => "Accrediti",
            ],
            [
                "slug"     => "professione-whos-coming",
                "singular" => "Professione",
                "plural"   => "Professioni",
            ],
            [
                "slug"     => "paese-whos-coming",
                "singular" => "Paese",
                "plural"   => "Paesi",
            ],
        ];

        foreach ($taxonomies as $tax) {
            register_extended_taxonomy(
                $tax["slug"],
                ["whos-coming"],
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
