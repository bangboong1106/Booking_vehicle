@include('layouts.backend.elements.column_config._list',[
    'entity'=>'repair_ticket',
    'is_action' => true,
    'is_show_history' => false,
    'attributes' => getColumnConfig("repair_ticket"), 
    'configList'=> isset($configList) ? $configList : []])