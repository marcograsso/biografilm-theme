<?php
return [
    'post_type'      => ['proiezione'],
    'post_status'    => ['publish'],
    'posts_per_page' => 100,
    'meta_query'     => [
        'data_clause' => [
            'key'     => 'data',
            'compare' => 'EXISTS',
        ],
        'orario_clause' => [
            'key'     => 'orario',
            'compare' => 'EXISTS',
        ],
    ],
    'orderby' => [
        'data_clause'   => 'ASC',
        'orario_clause' => 'ASC',
    ],
];
