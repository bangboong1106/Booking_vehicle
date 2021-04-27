@include('layouts.backend.elements.column_config._list',[
    'entity'=>'quota',
    'is_action' => true,
    'is_show_history' => false,
    'attributes' => getColumnConfig("quota"), 
    'configList'=> isset($configList) ? $configList : []])
{{-- @if(!$entities->total() || $entities->total() ==0)
    <div class="empty-box">
        <span><i>Không thể tìm thấy dữ liệu trên chương trình</i></span>
        <div class="wrap-btn">
            <div class="btn" id="btn-add-empty">
                <a href="{{backUrl($routePrefix.'.create') }}">
                    <i class="fa fa-plus"><span>Thêm định mức</span></i>
                </a>
            </div>
        </div>
    </div>
@else
    @foreach($entities as $index=>$entity)
        <tr class="{{$index %2 != 0 ? 'even' :'odd'}} {{isset($selectedItem) && in_array($entity->id, $selectedItem) ?
            'row-selected' : '' }}" data-id="{{$entity->id}}">
            @include('layouts.backend.elements.list_to_checkbox', ['id' => $entity->id])
            @include('layouts.backend.elements.list_to_action')
            <td class="text-middle" data-name="true">
                <a class="detail-toggle" href="#">
                    {{$entity->quota_code}}
                </a>
            </td>
            <td class="text-left text-middle">{!! $entity->name !!}</td>
            <td class="text-left text-middle">
                <a href="#" class="admin-detail" data-id="{!!$entity->vehicle_group_id  !!}"
                   data-show-url="{!! route("vehicle-group.show", isset($entity->vehicle_group_id) ? $entity->vehicle_group_id : 0) !!}">
                    {!! $entity->vehicleGroup ? $entity->vehicleGroup->name :'' !!}
                </a>
            </td>
            <td class="text-left text-middle">{!! $entity->title? str_replace('-',' - ',$entity->title) :'' !!}</td>
            <td class="text-right text-middle">{!! numberFormat($entity->total_cost) !!}</td>
            <td class="text-right text-middle">{!! numberFormat($entity->distance) !!}</td>
        </tr>
    @endforeach
@endif --}}