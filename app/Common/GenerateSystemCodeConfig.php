<?php
return [
    ['table' => 'orders', 'attribute' => 'order_code', 'type' => config('constant.sc_order')],
    ['table' => 'customer', 'attribute' => 'customer_code', 'type' => config('constant.sc_customer')],
    ['table' => 'locations', 'attribute' => 'code', 'type' => config('constant.sc_location')],
    ['table' => 'drivers', 'attribute' => 'code', 'type' => config('constant.sc_driver')],
    ['table' => 'goods_type', 'attribute' => 'code', 'type' => config('constant.sc_good_type')],
    ['table' => 'goods_unit', 'attribute' => 'code', 'type' => config('constant.sc_good_unit')],
    ['table' => 'vehicle_team', 'attribute' => 'code', 'type' => config('constant.sc_vehicle_team')],
    ['table' => 'm_vehicle_group', 'attribute' => 'code', 'type' => config('constant.sc_vehicle_group')],
    ['table' => 'routes', 'attribute' => 'route_code', 'type' => config('constant.sc_route')],
    ['table' => 'quota', 'attribute' => 'quota_code', 'type' => config('constant.sc_quota')],
    ['table' => 'order_customer', 'attribute' => 'code', 'type' => config('constant.sc_order_customer')],
    ['table' => 'customer_group', 'attribute' => 'code', 'type' => config('constant.sc_customer_group')],
    ['table' => 'price_quote', 'attribute' => 'code', 'type' => config('constant.sc_price_quote')],
    ['table' => 'payroll', 'attribute' => 'code', 'type' => config('constant.sc_payroll')],
    ['table' => 'location_group', 'attribute' => 'code', 'type' => config('constant.sc_location_group')],
    ['table' => 'repair_ticket', 'attribute' => 'code', 'type' => config('constant.sc_repair_ticket')],
    ['table' => 'goods_group', 'attribute' => 'code', 'type' => config('constant.sc_goods_group')],
    ['table' => 'partner', 'attribute' => 'code', 'type' => config('constant.sc_partner')],
    ['table' => 'customer_default_data', 'attribute' => 'code', 'type' => config('constant.sc_customer_default')],

];
