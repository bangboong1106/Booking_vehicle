@component('layouts.backend.elements.excel._import_list', [
    'entities' => $entities, 
    'excelColumnMappingConfigs' =>$excelColumnMappingConfigs,
     'nest_property' => 'order_locations',
     'extend_list' => $goodsList,
     'nested_extend_property' => 'order_goods'
     ])
    @slot('header')
        @foreach ($goodsList as $goodsItem)
            <th style="width: 150px"><span>{{ $goodsItem }}</span></th>
        @endforeach
    @endslot
@endcomponent
