@include('layouts.backend.elements.column_config._list',[
    'entity'=>'goods_type',
    'is_action' => true,
    'is_show_history' => false,
    'attributes' => getColumnConfig("goods_type"),
    'configList'=> isset($configList) ? $configList : []])