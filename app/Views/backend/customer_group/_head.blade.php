@include('layouts.backend.elements.column_config._head',[
    'entity'=>'customer_group',
    'is_action' => true,
    'is_show_history' => false,
    'attributes' => getColumnConfig("customer_group"), 
    'configList'=> isset($configList) ? $configList : []])