@include('layouts.backend.elements.column_config._list',[
    'entity'=>'vehicle_team',
    'is_action' => true,
    'is_show_history' => false,
    'attributes' => getColumnConfig("partner_vehicle_team"),
    'configList'=> isset($configList) ? $configList : []])