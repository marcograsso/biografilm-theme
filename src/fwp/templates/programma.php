<?php if (have_posts()): ?>
    <?php
    $site   = new \Timber\Site();
    $groups = [];
    while (have_posts()):
        the_post();
        $id        = get_the_ID();
        $post_type = get_post_type($id);

        if ($post_type === 'eventi-programma') {
            $orario = get_post_meta($id, "orario_inizio", true);
        } else {
            $orario = get_post_meta($id, "orario", true);
        }

        $hour = $orario ? (int) substr($orario, 0, 2) : "";
        $key  = $hour !== "" ? sprintf("%02d:00", $hour) : "__no_time__";

        $groups[$key][] = [
            'post'      => Timber\Timber::get_post($id),
            'post_type' => $post_type,
        ];
    endwhile;

    ksort($groups);
    ?>
    <div data-programma-grid class="w-full">
        <?php
        $first_group = true;
        foreach ($groups as $hour_label => $items): ?>
            <div class="flex <?= $first_group
                ? "border-t border-t-stroke"
                : "" ?>"><?php $first_group = false; ?>
                <?php if ($hour_label !== "__no_time__"): ?>
                    <div class="border-r-stroke border-b-stroke w-20 lg:w-30 shrink-0 border-r border-b">
                        <div data-sticky-hour class="flex justify-center pt-10 pb-10">
                            <h3 class="display-h6">
                                <?= esc_html($hour_label) ?>
                            </h3>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="border-r-stroke border-b-stroke w-20 lg:w-30 shrink-0 border-r border-b"></div>
                <?php endif; ?>
                <div class="min-w-0 flex-1">
                    <?php foreach ($items as $item):
                        if ($item['post_type'] === 'eventi-programma') {
                            Timber\Timber::render(
                                "parts/cards/eventi-programma-card.twig",
                                ["evento" => $item['post']],
                            );
                        } else {
                            Timber\Timber::render(
                                "parts/cards/program-card.twig",
                                ["proiezione" => $item['post'], "site" => $site],
                            );
                        }
                    endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p class="text-tight  text-center text-xl font-semibold text-black py-30">
        Nessun risultato in questa data per i filtri selezionati.<br>Prova a modificare la ricerca o resetta i filtri.
    </p>
<?php endif; ?>
