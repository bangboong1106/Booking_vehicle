@include('layouts.backend.elements.column_config._head',[
    'entity'=>'report_schedule',
    'is_action' => true,
    'is_show_history' => false,
    'attributes' => getColumnConfig("report_schedule"),
    'configList'=> isset($configList) ? $configList : []])