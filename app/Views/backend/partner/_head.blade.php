@include('layouts.backend.elements.column_config._head',[
    'entity'=>'partner',
    'is_action' => true,
    'attributes' => getColumnConfig("partner"),
    'configList'=> isset($configList) ? $configList : []])