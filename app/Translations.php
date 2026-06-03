<?php

namespace App;

/**
 * Registers hardcoded theme strings with Polylang so they appear in
 * Polylang → String Translations in the admin. Translate them there.
 *
 * Add new strings to $strings below, then visit the Polylang Strings
 * Translations screen to enter the English (or other language) values.
 */
class Translations
{
    private static array $strings = [
        // Archive pages
        "Sezioni",
        "Sezione",
        "Tutti i film",

        // Single film — info table labels
        "Regista",
        "Registi",
        "Titolo alternativo",
        "Titoli alternativi",
        "Titolo aggiuntivo",
        "Titoli aggiuntivi",
        "Titolo/Regista...",
        "Durata",
        "Paese",
        "Anno",
        "Genere",
        "Formato",
        "Tipologia",
        "Area tematica",
        "Aree tematiche",

        // Single film — screenings
        "Programmazione",
        "oppure",
        "Compra biglietto",
        "Guarda online su MyMovies",

        // Related content
        "Scopri anche",

        // Filter UI
        "Ordina per",
        "Titolo (A-Z)",
        "Titolo (Z-A)",
        "Filtri",
        "FILTRI",
        "Apri filtri",
        "Chiudi filtri",
        "Rimuovi filtri",
        "Vedi risultati",

        // Carousel / slider navigation
        "Precedente",
        "Successivo",
        "Chiudi",

        // Empty-state messages
        "Nessun risultato per i filtri selezionati.",
        "Prova a modificare la ricerca o resetta i filtri.",
        "Nessun documento trovato.",
        "Nessuna sezione trovata.",
        "Nessuna news trovata.",
        "Nessuna ospitalità trovata.",

        // General UI
        "Gestione consenso",
        "Mostra di più",
        "Mostra meno",
        "Informazioni utili",
        "Informazioni",
        "Scopri di più",

        // Archive / breadcrumb labels — eventi & progetti
        "eventi",
        "Eventi",
        "Eventi Campus",
        "Progetti e formazione",

        // Single evento / progetto — info labels
        "Data",
        "Luogo",
        "Relatori / Ospiti",
        "Descrizione",
        "Periodo",
        "Status",
        "Stato",
        "Target",
        "Tipo di progetto",
        "Tipo di evento",
        "Relatori / Tutor",
        "Beneficiari",
        "Restituzione finale",

        // Search results
        "Risultati",
        "Tipologia risultato",
        "risultati trovati per la ricerca",
        "Nessun risultato trovato.",
        "Pagina",

        // 404
        "Pagina non trovata",
        "La pagina che cerchi non esiste o è stata spostata.",
        "Torna alla home",

        // Single sezione / news / related
        "Film in sezione",
        "Leggi anche",
        "Proposte editoriali",

        // Single whos-coming — info labels
        "Nome",
        "Area Professionale",
        "Area professionale",
        "Accrediti",
        "Azienda",
        "Accredito",
        "Cerca...",
        "Link utili",

        // Single proposta editoriale — info labels
        "Autore",
        "Autori",
        "Traduttore",
        "Editore",
        "Collana",
        "Pagine",
        "Lingua originale",
    ];

    public static function register(): void
    {
        if (!function_exists("pll_register_string")) {
            return;
        }

        foreach (self::$strings as $string) {
            pll_register_string($string, $string, "Theme");
        }
    }
}
