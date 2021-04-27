@include('layouts.backend.elements.column_config._head',[
    'entity'=>'contact',
    'is_action' => true,
    'is_show_history' => false,
    'attributes' => getColumnConfig("contact"), 
    'configList'=> isset($configList) ? $configList : []])
