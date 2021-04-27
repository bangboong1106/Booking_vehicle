@include('layouts.backend.elements.column_config._head',[
    'entity'=>'location',
    'is_action' => true,
    'is_show_history' => false,
    'attributes' => getColumnConfig("location"), 
    'configList'=> isset($configList) ? $configList : []])