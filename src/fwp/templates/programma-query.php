<?php
return [
    'post_type'      => ['proiezione', 'eventi-programma'],
    'post_status'    => ['publish'],
    'posts_per_page' => 20,
    'meta_query'     => [
        'relation'    => 'AND',
        'data_clause' => [
            'key'     => 'data',
            'compare' => 'EXISTS',
        ],
        [
            'relation' => 'OR',
            [
                'key'     => 'escludi_dal_programma',
                'compare' => 'NOT EXISTS',
            ],
            [
                'key'     => 'escludi_dal_programma',
                'value'   => '1',
                'compare' => '!=',
            ],
        ],
    ],
    'orderby' => [
        'data_clause' => 'ASC',
    ],
];
