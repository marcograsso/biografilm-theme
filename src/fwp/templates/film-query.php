<?php
return [
    "post_type" => ["film"],
    "post_status" => ["publish"],
    "meta_query" => [
        "sort_0" => [
            "key" => "sezione",
            "type" => "CHAR",
        ],
    ],
    "orderby" => [
        "sort_0" => "ASC",
    ],
];
