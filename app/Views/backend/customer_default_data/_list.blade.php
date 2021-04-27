@include('layouts.backend.elements.column_config._list',[
    'entity'=>'customer_default_data',
    'is_action' => true,
    'is_show_history' => false,
    'attributes' => getColumnConfig("customer_default_data"),
    'configList'=> isset($configList) ? $configList : []])