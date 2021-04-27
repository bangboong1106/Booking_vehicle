@include('layouts.backend.elements.column_config._head',[
    'entity'=>'accessory',
    'is_action' => true,
    'is_show_history' => false,
    'attributes' => getColumnConfig("accessory"),
    'configList'=> isset($configList) ? $configList : []])