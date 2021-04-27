@include('layouts.backend.elements.column_config._list',[
    'entity'=>'accessory',
    'is_action' => true,
    'is_show_history' => false,
    'attributes' => getColumnConfig("accessory"), 
    'configList'=> isset($configList) ? $configList : []])