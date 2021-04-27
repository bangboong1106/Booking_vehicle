<?php
if (App::runningInConsole()) {
    return false;
}
$breadcrumbs = [
    [
        'type' => 'index',
        'name' => 'dashboard.main',
        'screen' => 'dashboard',
        'allow_link' => true,
        'childs' => [
            [
                'type' => 'resource',
                'screen' => 'admin'
            ],
            [
                'type' => 'resource',
                'screen' => 'province'
            ],
            [
                'type' => 'resource',
                'screen' => 'district'
            ],
            [
                'type' => 'resource',
                'screen' => 'ward'
            ],
            [
                'type' => 'resource',
                'screen' => 'role'
            ],
            [
                'type' => 'resource',
                'screen' => 'vehicle-group'
            ],
            [
                'type' => 'resource',
                'screen' => 'vehicle-team'
            ],
            [
                'type' => 'resource',
                'screen' => 'receipt-payment'
            ],
            [
                'type' => 'resource',
                'screen' => 'driver'
            ],
            [
                'type' => 'resource',
                'screen' => 'driver-config-file'
            ],
            [
                'type' => 'resource',
                'screen' => 'location'
            ],
            [
                'type' => 'resource',
                'screen' => 'vehicle'
            ],
            [
                'type' => 'resource',
                'screen' => 'vehicle-config-file'
            ],
            [
                'type' => 'resource',
                'screen' => 'vehicle-config-specification'
            ],
            [
                'type' => 'resource',
                'screen' => 'customer'
            ],
            [
                'type' => 'resource',
                'screen' => 'order'
            ],
            [
                'type' => 'resource',
                'screen' => 'goods-type'
            ],
            [
                'type' => 'resource',
                'screen' => 'contract-type'
            ],
            [
                'type' => 'resource',
                'screen' => 'system-code-config'
            ],
            [
                'type' => 'resource',
                'screen' => 'contact'
            ],
            [
                'type' => 'resource',
                'screen' => 'route'
            ],
            [
                'type' => 'resource',
                'screen' => 'quota'
            ],
            [
                'type' => 'resource',
                'screen' => 'system-config'
            ],
            [
                'type' => 'resource',
                'screen' => 'notification-log'
            ],
            [
                'type' => 'resource',
                'screen' => 'order-customer'
            ],
        ]
    ]
];
breadcrumb_register($breadcrumbs);
