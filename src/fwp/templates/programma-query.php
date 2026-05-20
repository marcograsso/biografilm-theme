<?php
return [
    'post_type'      => ['proiezione', 'eventi-programma'],
    'post_status'    => ['publish'],
    'posts_per_page' => 20,
    'meta_query'     => [
        'data_clause' => [
            'key'     => 'data',
            'compare' => 'EXISTS',
        ],
    ],
    'orderby' => [
        'data_clause' => 'ASC',
    ],
];
