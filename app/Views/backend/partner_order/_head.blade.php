@include('layouts.backend.elements.column_config._head',[
'entity'=>'order',
'is_action' => false,
'dbclick' => false,
'attributes' => getColumnConfig("partner_order"),
'configList'=> isset($configList) ? $configList : []])
