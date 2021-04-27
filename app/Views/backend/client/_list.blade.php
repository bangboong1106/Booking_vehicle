@include('layouts.backend.elements.column_config._list',[
    'entity'=>'client',
    'is_action' => true,
    'attributes' => getColumnConfig("client"),
    'configList'=> isset($configList) ? $configList : []])