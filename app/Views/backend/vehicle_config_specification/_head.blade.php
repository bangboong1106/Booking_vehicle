@include('layouts.backend.elements.column_config._head',[
    'entity'=>'vehicle_config_specification',
    'is_action' => true,
    'is_show_history' => false,
    'attributes' => getColumnConfig("vehicle_config_specification"),
    'configList'=> isset($configList) ? $configList : []])