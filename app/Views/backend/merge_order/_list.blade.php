@include('layouts.backend.elements.column_config._list',[
'entity'=>'merge_order',
'is_action' => false,
'dbclick' => false,
'attributes' => getColumnConfig("merge_order"),
'configList'=> isset($configList) ? $configList : []])
