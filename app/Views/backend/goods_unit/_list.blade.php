@include('layouts.backend.elements.column_config._list',[
    'entity'=>'goods_unit',
    'is_action' => true,
    'is_show_history' => false,
    'attributes' => getColumnConfig("goods_unit"),
    'configList'=> isset($configList) ? $configList : []])