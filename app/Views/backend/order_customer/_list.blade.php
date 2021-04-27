@include('layouts.backend.elements.column_config._list',[
    'entity'=>'order_customer',
    'is_action' => false,
    'is_show_history' => false,
    'attributes' => getColumnConfig("order_customer"), 
    'configList'=> isset($configList) ? $configList : []])
