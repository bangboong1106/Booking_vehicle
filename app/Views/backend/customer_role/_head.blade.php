@include('layouts.backend.elements.column_config._head',[
    'entity'=>'role',
    'is_action' => true,
    'is_show_history' => false,
    'attributes' => getColumnConfig("role"), 
    'configList'=> isset($configList) ? $configList : []])