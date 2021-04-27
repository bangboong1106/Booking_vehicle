@include('layouts.backend.elements.column_config._head',[
    'entity'=>'contract',
    'is_action' => true,
    'is_show_history' => false,
    'attributes' => getColumnConfig("contract"), 
    'configList'=> isset($configList) ? $configList : []])