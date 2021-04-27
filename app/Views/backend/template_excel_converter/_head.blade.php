@include('layouts.backend.elements.column_config._head',[
    'entity'=>'template_excel_converter',
    'is_action' => true,
    'is_show_history' => false,
    'attributes' => getColumnConfig("template_excel_converter"),

    'configList'=> isset($configList) ? $configList : []])