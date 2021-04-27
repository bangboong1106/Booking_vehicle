@include('layouts.system_code_config.elements.column_config._head',[
    'entity'=>'system_code_config',
    'is_action' => true,
    'is_show_history' => false,
    'attributes' => getColumnConfig("system_code_config"),
    'configList'=> isset($configList) ? $configList : []])