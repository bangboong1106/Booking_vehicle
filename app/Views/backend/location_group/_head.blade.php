@include('layouts.backend.elements.column_config._head',[
    'entity'=>'location_group',
    'is_action' => true,
    'is_show_history' => false,
    'attributes' => getColumnConfig("location_group"), 
    'configList'=> isset($configList) ? $configList : []])