@include('layouts.backend.elements.column_config._head',[
    'entity'=>'order_price',
    'is_action' => false,
    'is_show_history' => false,
    'attributes' => getColumnConfig("order_price"), 
    'configList'=> isset($configList) ? $configList : []])