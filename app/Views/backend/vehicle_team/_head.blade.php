@include('layouts.backend.elements.column_config._head',[
    'entity'=>'vehicle_team',
    'is_action' => true,
    'is_show_history' => false,
    'attributes' => getColumnConfig("vehicle_team"), 
    'configList'=> isset($configList) ? $configList : []])