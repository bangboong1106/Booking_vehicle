@include('layouts.backend.elements.column_config._head',[
    'entity'=>'template',
    'is_action' => true,
    'is_show_history' => false,
    'attributes' => getColumnConfig("template"),
    'configList'=> isset($configList) ? $configList : []])