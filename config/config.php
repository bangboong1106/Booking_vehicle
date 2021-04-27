<?php
return [
    // file info
    'file' => array(
        'default' => [
            'image' => [
                'ext' => array('jpeg', 'jpg', 'png', 'gif')
            ]
        ],
        'admin' => [
            'avatar' => array(
                'size' => ['min' => 0.01, 'max' => 2], // MB
                'ext' => array('jpeg', 'jpg', 'png', 'gif')
            )
        ]
    ),
    'csv' => [
        'export' => [
            'admin' => [
                'filename_prefix' => 'Admin',
                'header' =>
                    [
                        'id' => 'ID',
                        'email' => 'Email',
                        'avatar' => 'Avatar',
                        'ins_date' => 'Created at',
                        'upd_date' => 'Updated at',
                    ],
            ]
        ]
    ]
];
