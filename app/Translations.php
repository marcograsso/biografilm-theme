<?php

namespace App;

/**
 * Static Polylang string translations.
 *
 * Add entries to $strings below — translations are written to the DB only
 * when the array changes (detected by hash), so there is no overhead on
 * normal page loads.
 *
 * Format:
 *   'Original Italian string' => [
 *       'en' => 'English translation',
 *   ],
 */
class Translations
{
    private static array $strings = [
        // Archive pages
        "Sezioni" => ["en" => "Sections"],
        "Sezione" => ["en" => "Section"],
        "Tutti i film" => ["en" => "All Films"],

        // Single film — info table labels
        "Regista" => ["en" => "Director"],
        "Registi" => ["en" => "Directors"],
        "Titolo alternativo" => ["en" => "Alternative title"],
        "Titoli alternativi" => ["en" => "Alternative titles"],
        "Titolo aggiuntivo" => ["en" => "Additional title"],
        "Titoli aggiuntivi" => ["en" => "Additional titles"],
        "Titolo/Regista..." => ["en" => "Title/Director..."],
        "Durata" => ["en" => "Duration"],
        "Paese" => ["en" => "Country"],
        "Anno" => ["en" => "Year"],
        "Genere" => ["en" => "Genre"],
        "Formato" => ["en" => "Format"],
        "Tipologia" => ["en" => "Typology"],
        "Area tematica" => ["en" => "Thematic area"],
        "Aree tematiche" => ["en" => "Thematic areas"],

        // Single film — screenings
        "Programmazione" => ["en" => "Screenings"],
        "oppure" => ["en" => "or"],
        "Compra biglietto" => ["en" => "Buy ticket"],
        "Guarda online su MyMovies" => ["en" => "Watch online on MyMovies"],

        // Related content
        "Scopri anche" => ["en" => "Discover more"],

        // Filter UI
        "Ordina per" => ["en" => "Sort by"],
        "Titolo (A-Z)" => ["en" => "Title (A-Z)"],
        "Titolo (Z-A)" => ["en" => "Title (Z-A)"],
        "Filtri" => ["en" => "Filters"],
        "FILTRI" => ["en" => "FILTERS"],
        "Apri filtri" => ["en" => "Open filters"],
        "Chiudi filtri" => ["en" => "Close filters"],
        "Rimuovi filtri" => ["en" => "Remove filters"],
        "Vedi risultati" => ["en" => "View results"],

        // Carousel / slider navigation
        "Precedente" => ["en" => "Previous"],
        "Successivo" => ["en" => "Next"],
        "Chiudi" => ["en" => "Close"],

        // Empty-state messages
        "Nessun risultato per i filtri selezionati." => [
            "en" => "No results for the selected filters.",
        ],
        "Prova a modificare la ricerca o resetta i filtri." => [
            "en" => "Try modifying your search or reset the filters.",
        ],
        "Nessun documento trovato." => ["en" => "No documents found."],
        "Nessuna sezione trovata." => ["en" => "No sections found."],
        "Nessuna news trovata." => ["en" => "No news found."],
        "Nessuna ospitalità trovata." => ["en" => "No accommodation found."],

        // General UI
        "Informazioni utili" => ["en" => "Useful information"],
        "Informazioni" => ["en" => "Information"],
        "Scopri di più" => ["en" => "Find out more"],

        // Archive / breadcrumb labels — eventi & progetti
        "eventi" => ["en" => "Events"],
        "Eventi" => ["en" => "Events"],
        "Eventi Campus" => ["en" => "Campus Events"],
        "Progetti e formazione" => ["en" => "Projects and education"],

        // Single evento / progetto — info labels
        "Data" => ["en" => "Date"],
        "Luogo" => ["en" => "Venue"],
        "Relatori / Ospiti" => ["en" => "Speakers / Guests"],
        "Descrizione" => ["en" => "Description"],
        "Periodo" => ["en" => "Period"],
        "Status" => ["en" => "Status"],
        "Stato" => ["en" => "status"],
        "Target" => ["en" => "Target"],
        "Tipo di progetto" => ["en" => "Project type"],
        "Tipo di evento" => ["en" => "event type"],
        "Relatori / Tutor" => ["en" => "Speakers / Tutors"],
        "Beneficiari" => ["en" => "Beneficiaries"],
        "Restituzione finale" => ["en" => "Final presentation"],

        // Search results
        "Risultati" => ["en" => "Results"],
        "Tipologia risultato" => ["en" => "Result type"],
        "risultati trovati per la ricerca" => ["en" => "results found for the search"],
        "Nessun risultato trovato." => ["en" => "No results found."],
        "Pagina" => ["en" => "Page"],

        // 404
        "Pagina non trovata" => ["en" => "Page not found"],
        "La pagina che cerchi non esiste o è stata spostata." => ["en" => "The page you're looking for doesn't exist or has been moved."],
        "Torna alla home" => ["en" => "Back to home"],

        // Single sezione / news / related
        "Film in sezione" => ["en" => "Films in section"],
        "Leggi anche" => ["en" => "Read also"],
        "Proposte editoriali" => ["en" => "Editorial proposals"],

        // Single whos-coming — info labels
        "Nome" => ["en" => "Name"],
        "Area Professionale" => ["en" => "Professional Area"],
        "Area professionale" => ["en" => "Professional Area"],
        "Accrediti" => ["en" => "Accreditations"],
        "Azienda" => ["en" => "Company"],
        "Accredito" => ["en" => "Accreditation"],
        "Cerca..." => ["en" => "Search..."],
        "Link utili" => ["en" => "Useful links"],

        // Single proposta editoriale — info labels
        "Autore" => ["en" => "Author"],
        "Autori" => ["en" => "Authors"],
        "Traduttore" => ["en" => "Translator"],
        "Editore" => ["en" => "Publisher"],
        "Collana" => ["en" => "Series"],
        "Pagine" => ["en" => "Pages"],
        "Lingua originale" => ["en" => "Original language"],
    ];

    /**
     * Register all strings with Polylang so they appear in the admin UI.
     * Call this on `init`.
     */
    public static function register(): void
    {
        if (!function_exists("pll_register_string")) {
            return;
        }

        foreach (array_keys(self::$strings) as $original) {
            pll_register_string($original, $original, "Theme");
        }
    }

    /**
     * Write translations to the DB via PLL_MO.
     * Skipped entirely when the strings array has not changed since the last run.
     * Call this on `init` at a priority > 1 (after Polylang itself initialises).
     */
    public static function inject(): void
    {
        if (!function_exists("PLL") || !PLL() || !isset(PLL()->model)) {
            return;
        }

        $hash = md5(serialize(self::$strings));
        if (get_option("_biografilm_translations_hash") === $hash) {
            return;
        }

        foreach (self::$strings as $original => $languages) {
            foreach ($languages as $slug => $translation) {
                $language = PLL()->model->get_language($slug);
                if (!$language) {
                    continue;
                }

                $mo = new \PLL_MO();
                $mo->import_from_db($language);
                $mo->add_entry($mo->make_entry($original, $translation));
                $mo->export_to_db($language);
            }
        }

        update_option("_biografilm_translations_hash", $hash, false);
    }
}
