<?php

use Extended\ACF\Fields\File;
use Extended\ACF\Fields\FlexibleContent;
use Extended\ACF\Fields\Group;
use Extended\ACF\Fields\Image;
use Extended\ACF\Fields\Layout;
use Extended\ACF\Fields\Link;
use Extended\ACF\Fields\Oembed;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\TrueFalse;

return [
    Tab::make("Generale", "generale_tab"),
    Text::make("Titolo", "title"),
    Link::make("Link", "link")->format("array"),
    Tab::make("Media", "media_tab"),
    FlexibleContent::make("Media", "items")
        ->button("Aggiungi elemento")
        ->layouts([
            Layout::make("Immagine", "immagine")
                ->layout("block")
                ->fields([
                    Image::make("Immagine", "immagine")
                        ->format("array"),
                ]),
            Layout::make("Video (file)", "video_file")
                ->layout("block")
                ->fields([
                    File::make("Video", "file")
                        ->format("array")
                        ->helperText("Carica un file video (mp4, mov, ecc.)."),
                ]),
            Layout::make("Video (URL)", "video_url")
                ->layout("block")
                ->fields([
                    Oembed::make("URL", "url")
                        ->helperText("Incolla un URL di YouTube, Vimeo o altro servizio supportato."),
                ]),
        ]),
    Tab::make("Impostazioni", "impostazioni_tab"),
    TrueFalse::make("Sempre pari", "always_even")
        ->stylized()
        ->helperText("Se attivo e il numero di contenuti è dispari, l'ultimo elemento viene nascosto nelle griglie a 2 colonne e mostrato solo dalla griglia a 4 colonne (3xl)."),
    Group::make("Bordi", "borders_group")
        ->layout("row")
        ->fields([
            TrueFalse::make("Titolo — bordo superiore", "border_top")
                ->stylized()
                ->default(false),
            TrueFalse::make("Titolo — bordo inferiore", "border_bottom")
                ->stylized()
                ->default(false),
            TrueFalse::make("Media — bordo superiore", "cards_border_top")
                ->stylized()
                ->default(false),
            TrueFalse::make("Media — bordo inferiore", "cards_border_bottom")
                ->stylized()
                ->default(false),
            TrueFalse::make("Celle vuote — bordo inferiore", "filler_border_bottom")
                ->stylized()
                ->default(false),
        ]),
];
