@include('layouts.backend.elements.column_config._list',[
    'entity'=>'vehicle_config_specification',
    'is_action' => true,
    'is_show_history' => false,
    'attributes' => $attributes, 
    'attributes' => getColumnConfig("vehicle_config_specification"),
    'configList'=> isset($configList) ? $configList : []])
{{-- @if(!$entities->total()|| $entities->total() ==0 )
    <div class="empty-box"><span><i>Không thể tìm thấy dữ liệu trên chương trình</i></span></div>
@else
    @foreach($entities as $index=>$entity)

        @if($entity->is_required == 0)
            <tr class="{{$index %2 != 0 ? 'even' :'odd'}} {{isset($selectedItem) && in_array($entity->id, $selectedItem) ?
                'row-selected' : '' }}" data-id="{{$entity->id}}">
                @include('layouts.backend.elements.list_to_checkbox', ['id' => $entity->id])
                @include('layouts.backend.elements.list_to_action')
                <td class="text-middle">
                    <a href="{{backUrl('vehicle-config-specification.edit', ['id' => $entity->id, 'backUrlKey' => isset($backUrlKey) ? $backUrlKey : null])}}">{{$entity->name}}</a>
                </td>
                <td class="text-middle">{{$entity->getType()}}</td>
                <td class="text-middle">
                    {!! $entity->active == 1 ? '<i class="fa fa-check" aria-hidden="true"></i>':'' !!}
                </td>
            </tr>
        @endif

    @endforeach
@endif --}}