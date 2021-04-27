@include('layouts.backend.elements.column_config._list',[
    'entity'=>'driver',
    'is_action' => true,
    'attributes' => getColumnConfig("partner_driver"),
    'configList'=> isset($configList) ? $configList : []])
