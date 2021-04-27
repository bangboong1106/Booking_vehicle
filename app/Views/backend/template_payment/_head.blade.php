@include('layouts.backend.elements.column_config._head',[
    'entity'=>'template_payment',
    'is_action' => false,
    'is_show_history' => false,
    'attributes' => getColumnConfig("template_payment"),

    'configList'=> isset($configList) ? $configList : []])