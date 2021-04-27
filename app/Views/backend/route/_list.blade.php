@include('layouts.backend.elements.column_config._list',[
    'entity'=>'route',
    'is_action' => true,
    'is_show_history' => false,
    'attributes' => getColumnConfig("route"), 
    'configList'=> isset($configList) ? $configList : []])
{{-- 
@if(!$entities->total() || $entities->total() == 0)
    <div class="empty-box">
        <span><i>Không thể tìm thấy dữ liệu trên chương trình</i></span>
        <div class="wrap-btn">
            <div class="btn" id="btn-add-empty">
                <a href="{{backUrl($routePrefix.'.create') }}">
                    <i class="fa fa-plus"><span>Thêm chuyến xe</span></i>
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
                    {{$entity->route_code}}
                </a>
            </td>
            <td class="text-left text-middle">{!! $entity->name !!}</td>
            <td class="text-middle">
                     <span class="badge badge-{!! $entity->route_status == 1 ? 'success' :
                     ($entity->route_status == 2 ? 'danger': 'secondary') !!}">
                                        {!! $entity->getStatus() !!}
                                </span>
            </td>
            <td class="text-left text-middle">
                <a href="#" class="admin-detail"
                   data-id="{!! isset($entity->vehicle_id ) ? $entity->vehicle_id : 0  !!}"
                   data-show-url="{!! route("vehicle.show", isset($entity->vehicle_id) ? $entity->vehicle_id : 0)!!}">
                    {!! $entity->reg_no ? $entity->reg_no :"" !!}
                </a>
            </td>
            <td class="text-left text-middle">
                <a href="#" class="admin-detail" data-id="{!! isset($entity->driver_id) ? $entity->driver_id : 0  !!}"
                   data-show-url="{!! route("driver.show", isset($entity->driver_id) ? $entity->driver_id : 0) !!}">
                    {{$entity->primary_driver_name ? $entity->primary_driver_name :""}}
                </a>
            </td>
            <td class="text-left text-middle">{!! $entity->order_codes !!}</td>
            <td class="text-left text-middle">{!! $entity->destination_location_title !!}</td>
            <td class="text-left text-middle">{!! $entity->arrival_location_title !!}</td>
            <td class="text-left text-middle">{!! $entity->getIsApproved() !!}</td>
            <td class="text-right text-middle">{!! numberFormat($entity->final_cost)!!}</td>

            <td class="text-middle text-center">{{ $entity->getDateTime('ETD_date_reality', 'd-m-Y').' '.$entity->getDateTime('ETD_time_reality', 'H:i')}}</td>
            <td class="text-middle text-center">{{ $entity->getDateTime('ETA_date_reality', 'd-m-Y').' '.$entity->getDateTime('ETA_time_reality', 'H:i')}}</td>
            <td class="text-middle text-center">{{ $entity->getDateTime('ETD_date', 'd-m-Y').' '.$entity->getDateTime('ETD_time', 'H:i') }}</td>
            <td class="text-middle text-center">{{ $entity->getDateTime('ETA_date', 'd-m-Y').' '.$entity->getDateTime('ETA_time', 'H:i')}}</td>
            <td class="text-right text-middle">{{ $entity->gps_distance }}</td>
            <td class="text-right text-middle">{{ isset($entity->capacity_weight_ratio) ?
                $entity->capacity_weight_ratio . '%' : '' }}</td>
            <td class="text-right text-middle">{{ isset($entity->capacity_volume_ratio) ?
                $entity->capacity_volume_ratio . '%' : '' }}</td>
            <td class="text-left text-middle">{!! $entity->route_note !!}</td>
        </tr>
    @endforeach
@endif --}}