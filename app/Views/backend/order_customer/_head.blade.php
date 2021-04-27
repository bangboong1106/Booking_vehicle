@include('layouts.backend.elements.column_config._head',[
    'entity'=>'order_customer',
    'is_action' => true,
    'attributes' => getColumnConfig("order_customer"), 
    'configList'=> isset($configList) ? $configList : []])
