@include('layouts.backend.elements.column_config._list',[
    'entity'=>'admin',
    'is_action' => true,
    'is_show_history' => false,
    'attributes' => getColumnConfig("admin"), 
    'configList'=> isset($configList) ? $configList : []])