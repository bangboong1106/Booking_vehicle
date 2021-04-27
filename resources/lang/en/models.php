<?php

return [
    'common' => [
        'id' => 'ID',
        'ins_id' => 'Insert ID',
        'upd_id' => 'Update ID',
        'upd_date' => 'Update Date',
        'ins_date' => 'Insert Date',
        'del_flag' => 'Delete Flag',
    ],
    'dashboard' => [
        'name' => 'Dashboard',
    ],
    'category' => [
        'name' => 'Category',
        'attributes' => [
            'name' => 'category name',
            'description' => 'description',
            'image' => 'image'
        ],
    ],
    'admin' => [
        'name' => 'Admin',
        'attributes' => [
            'email' => 'Email',
            'password' => 'Password',
            'avatar' => 'Avatar',
            'password_note_text' => 'Leave blank if not change',
            'password_confirmation' => 'Password Confirmation',
            'role' => 'Role',
        ],
    ]
];
