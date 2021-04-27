<?php
return [
    'backend' => [
        'per_page' => [
            'default' => 50,
            'export_csv'=> 10000, // Export mặc định 10000 items
            'order_dashboard' => 20,
        ],
        'max_page' => [
            'default' => 10,
            'admin' => 110,
        ],
    ],
    'frontend' => [
        'per_page' => [
            'default' => 20,
            'admin' => 10,
        ],
        'max_page' => [
            'default' => 10,
            'admin' => 10,
        ]
    ],
    'api' => [
        'per_page' => [
            'default' => 20,
            'admin' => 10,
        ],
        'max_page' => [
            'default' => 10,
            'admin' => 10,
        ]
    ],
];
