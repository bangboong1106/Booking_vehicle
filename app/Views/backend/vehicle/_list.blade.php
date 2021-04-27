@include('layouts.backend.elements.column_config._list',[
    'entity'=>'vehicle',
    'is_action' => true,
    'attributes' => getColumnConfig("vehicle"), 
    'configList'=> isset($configList) ? $configList : []])
{{-- @if(!$entities->total()|| $entities->total() ==0)
    <div class="empty-box">
        <span><i>Không thể tìm thấy dữ liệu trên chương trình</i></span>
        <div class="wrap-btn">
            <div class="btn" id="btn-add-empty">
                <a href="{{backUrl($routePrefix.'.create') }}">
                    <i class="fa fa-plus"><span>Thêm xe</span></i>
                </a>
            </div>
        </div>
    </div>
@else
    @foreach($entities as $index=>$entity)
        <tr class="{{$index %2 != 0 ? 'even' :'odd'}} {{isset($selectedItem) && in_array($entity->id, $selectedItem) ?
            'row-selected' : '' }}" data-id="{{$entity->id}}">
            @include('layouts.backend.elements.list_to_checkbox', ['id' => $entity->id])
            @include('layouts.backend.elements.list_to_action', ['gps' => 'vehicle_plate'])
            <td class="text-middle" data-name="true">
                <a class="detail-toggle" href="#">
                    {{$entity->reg_no}}
                </a>
            </td>
            @if(isset($configList))
                @foreach($configList as $config)
                    @if($config['shown'])
                        @switch($config['name'])
                            @case('group_id')
                            <td class="text-middle">
                                <a href="#" class="admin-detail" data-id="{!!$entity->group_id  !!}"
                                    data-show-url="{!! route("vehicle-group.show", isset($entity->group_id) ? $entity->group_id : 0) !!}">
                                    {{$entity->tryGet('vehicleGroup')->name}}
                                 </a>
                                
                            </td>
                            @break
                            @case('drivers_name')
                            <td class="text-middle">{!! $entity->drivers_name !!}</td>
                            @break
                            @case('weight')
                            <td class="text-right">{{numberFormat($entity->weight)}}</td>
                            @break
                            @case('volume')
                            <td class="text-right">{{numberFormat($entity->volume)}}</td>
                            @break
                            @case('length_width_height')
                            <td class="text-right">
                                {{$entity->length !=null ? numberFormat($entity->length) : numberFormat(0)}}
                                * {{$entity->width !=null ? numberFormat($entity->width) : numberFormat(0)}}
                                * {{ $entity->height !=null ? numberFormat($entity->height) : numberFormat(0)}}
                            </td>
                            @break
                            @case('status')
                            <td class="text-middle">{{$entity->getStatus()}}</td>
                            @break
                            @case('type')
                            <td class="text-middle">{{$entity->getType()}}</td>
                            @break
                            @case('active')
                            <td class="text-middle">{{$entity->getActive()}}</td>
                            @break
                            @case('current_location')
                            <td class="text-middle">{!! empty($entity->current_location) ? '' : '<i class="fa fa-map-marker"
                                                        aria-hidden="true"></i>'.$entity->current_location !!}
                            </td>
                            @break
                            @case('category_of_barrel')
                            <td class="text-middle">{{$entity->tryGet('vehicleGeneralInfo')->category_of_barrel}}</td>
                            @break
                            @case('weight_lifting_system')
                            <td class="text-middle">{{$entity->tryGet('vehicleGeneralInfo')->weight_lifting_system}}</td>
                            @break
                            @case('max_fuel')
                            <td class="text-right">{{numberFormat($entity->tryGet('vehicleGeneralInfo')->max_fuel)}}</td>
                            @break
                            @case('max_fuel_with_goods')
                            <td class="text-right">{{numberFormat($entity->tryGet('vehicleGeneralInfo')->max_fuel_with_goods)}}</td>
                            @break
                            @case('register_year')
                            <td class="text-right">{{$entity->tryGet('vehicleGeneralInfo')->register_year}}</td>
                            @break
                            @case('brand')
                            <td class="text-middle">{{$entity->tryGet('vehicleGeneralInfo')->brand}}</td>
                            @break
                            @case('gps_company_id')
                            <td class="text-middle">{{$entity->tryGet('gpsCompany')->name}}</td>
                            @break
                            @case('ins_id')
                            <td class="text-middle">
                                {!! empty($entity->insUser) ? '' :
                                '<div class="person-circle" style="cursor: pointer;line-height: unset;">
                                <img src="'.route("file.getImage", [
                                   'id'=>empty($entity->insUser->avatar_id) ? '00000000-0000-0000-0000-000000000000': $entity->insUser->avatar_id,

                                 'width'=> 24, 'height' => 24] ).'" class="avatar">
                                </div><a href="#" class="admin-detail" data-id="'.$entity->ins_id.'" data-show-url="'.route("admin.show", $entity->ins_id).'"  >'.$entity->insUser->username.'</a>' !!}
                            </td>
                            @break
                            @case('upd_id')
                            <td class="text-middle">{!! empty($entity->updUser) ? '' :
                            '<div class="person-circle" style="cursor: pointer;line-height: unset;">
                             <img src="'.route("file.getImage", [
                              'id'=>empty($entity->updUser->avatar_id) ? '00000000-0000-0000-0000-000000000000': $entity->updUser->avatar_id,

                             'width'=> 24, 'height' => 24] ).'" class="avatar">
                            </div><a href="#" class="admin-detail" data-id="'.$entity->upd_id.'" data-show-url="'.route("admin.show", $entity->upd_id).'"  >'.$entity->updUser->username.'</a>'
                             !!}
                            </td>
                            @break
                            @case('ins_date')
                            <td class="text-center">
                                {!! $entity->getDateTime('ins_date', 'd-m-Y H:i') !!}</td>
                            @break
                            @case('upd_date')
                            <td class="text-center">
                                {!! $entity->getDateTime('upd_date', 'd-m-Y H:i') !!}</td>
                            @break
                            @case('repair_distance')
                            <td class="text-center">
                                {!! numberFormat($entity->repair_distance) !!}</td>
                            @break
                            @case('repair_date')
                            <td class="text-center">
                                {!! $entity->getDateTime('repair_date', 'd-m-Y H:i') !!}</td>
                            @break
                        @endswitch
                    @endif
                @endforeach
            @else
                <td class="text-middle">{{$entity->tryGet('vehicleGroup')->name}}</td>
                <td class="text-middle">{!! $entity->drivers_name !!}</td>
                <td class="text-right">{{numberFormat($entity->weight)}}</td>
                <td class="text-right">{{numberFormat($entity->volume)}}</td>
                <td class="text-right">
                    {{$entity->length !=null ? numberFormat($entity->length) : numberFormat(0)}}
                    * {{$entity->width !=null ? numberFormat($entity->width) : numberFormat(0)}}
                    * {{ $entity->height !=null ? numberFormat($entity->height) : numberFormat(0)}}
                </td>
            @endif
        </tr>
    @endforeach
@endif --}}