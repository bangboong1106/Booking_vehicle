@include('layouts.backend.elements.column_config._list',[
    'entity'=>'driver',
    'is_action' => true,
    'attributes' => getColumnConfig("driver"), 
    'configList'=> isset($configList) ? $configList : []])

{{-- @if(!$entities->total() || $entities->total() ==0)
    <div class="empty-box"><span><i>Không thể tìm thấy dữ liệu trên chương trình</i></span>
        <div class="wrap-btn">
            <div class="btn" id="btn-add-empty">
                <a href="{{backUrl($routePrefix.'.create') }}">
                    <i class="fa fa-plus"><span>Thêm tài xế</span></i>
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
                    {{$entity->code}}
                </a>
            </td>
            @if(isset($configList))
                @foreach($configList as $config)
                    @if($config['shown'])
                        @switch($config['name'])
                            @case('username')
                            <td class="text-middle">{{$entity->tryGet('adminUser')->username}}</td>
                            @break
                            @case('full_name')
                            <td class="text-middle">
                                <div class="person-circle chr-{{strtoupper($entity->full_name[0])}}"
                                     style="cursor: pointer;">{{strtoupper($entity->full_name[0])}}
                                </div>{{$entity->full_name}}</td>
                            @break
                            @case('email')
                            <td class="text-middle">
                                <a href="mailto:{{$entity->tryGet('adminUser')->email}}">{{$entity->tryGet('adminUser')->email}}</a>
                            </td>
                            @break
                            @case('mobile_no')
                            <td class="text-middle">
                                <a href="tel:{{$entity->mobile_no}}"><i class="fa fa-phone"
                                                                        aria-hidden="true"></i>{{$entity->mobile_no}}
                                </a>
                            </td>
                            @break
                            @case('id_no')
                            <td class="text-middle">{{$entity->id_no}}</td>
                            @break
                            @case('driver_license')
                            <td class="text-middle">{{$entity->driver_license}}</td>
                            @break
                            @case('sex')
                            <td class="text-middle">{{$entity->getSexText()}}</td>
                            @break
                            @case('birth_date')
                            <td class="text-center">{{$entity->getDateTime('birth_date', 'd-m-Y')}}</td>
                            @break
                            @case('vehicle_team_id')
                            <td class="text-middle">{!! $entity->vehicle_team_names !!}</td>
                            @break
                            @case('vehicles_reg_no')
                            <td class="text-middle">{!! $entity->vehicles_reg_no !!}</td>
                            @break
                            @case('work_date')
                            <td class="text-center">{{ $entity->getDateTime('work_date', 'd-m-Y')}}</td>
                            @break
                            @case('experience_drive')
                            <td class="text-right">{{$entity->experience_drive}}</td>
                            @break
                            @case('experience_work')
                            <td class="text-right">{{$entity->experience_work}}</td>
                            @break
                            @case('address')
                            <td class="text-middle">{{$entity->address}}</td>
                            @break
                            @case('hometown')
                            <td class="text-middle">{{$entity->hometown}}</td>
                            @break
                            @case('evaluate')
                            <td class="text-middle">{{$entity->evaluate}}</td>
                            @break
                            @case('rank')
                            <td class="text-middle">{{$entity->rank}}</td>
                            @break
                            @case('work_description')
                            <td class="text-middle">{{$entity->work_description}}</td>
                            @break
                            @case('note')
                            <td class="text-middle">{{$entity->note}}</td>
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
                        @endswitch
                    @endif
                @endforeach
            @else
                <td class="text-middle">{{$entity->tryGet('adminUser')->username}}</td>
                <td class="text-middle">
                    <div class="person-circle chr-{{strtoupper($entity->full_name[0])}}"
                         style="cursor: pointer;">{{strtoupper($entity->full_name[0])}}
                    </div>{{$entity->full_name}}</td>
                <td class="text-middle"><a href="tel:{{$entity->mobile_no}}"><i class="fa fa-phone"
                                                                                aria-hidden="true"></i>{{$entity->mobile_no}}
                    </a></td>
                <td class="text-middle">{{$entity->id_no}}</td>
            @endif
        </tr>
    @endforeach
@endif --}}