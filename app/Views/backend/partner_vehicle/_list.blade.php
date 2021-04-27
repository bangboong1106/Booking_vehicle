@include('layouts.backend.elements.column_config._list',[
    'entity'=>'vehicle',
    'is_action' => true,
    'attributes' => getColumnConfig("partner_vehicle"),
    'configList'=> isset($configList) ? $configList : []])