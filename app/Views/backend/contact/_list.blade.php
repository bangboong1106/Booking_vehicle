@include('layouts.backend.elements.column_config._list',[
    'entity'=>'contact',
    'is_action' => true,
    'is_show_history' => false,
    'attributes' => getColumnConfig("contact"), 
    'configList'=> isset($configList) ? $configList : []])
{{-- @if(!$entities->total())
    <div class="empty-box"><span><i>Không thể tìm thấy dữ liệu trên chương trình</i></span>
        <div class="wrap-btn">
            <div class="btn" id="btn-add-empty">
                <a href="{{backUrl($routePrefix.'.create') }}">
                    <i class="fa fa-plus"><span>Thêm hợp đồng</span></i>
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
                    <div class="person-circle chr-{{strtoupper($entity->contact_name[0])}}"
                         style="cursor: pointer;">{{strtoupper($entity->contact_name[0])}}
                    </div> {{$entity->contact_name}}
                </a>
            </td>
            <td class="text-middle"><a href="tel:{{$entity->phone_number}}"><i class="fa fa-phone"
                                                                               aria-hidden="true"></i>{{$entity->phone_number}}
                </a></td>
            <td class="text-middle">{!! $entity->email == '' ?  '' : '<a href="mailto:'.$entity->email.'"><i class="fa fa-envelope"
                                                                           aria-hidden="true"></i>'.$entity->email.'</a>' !!}
            </td>
            <td class="text-middle">
                <a target="_blank"
                   href="https://www.google.com/maps/search/?api=1&query={!! empty($entity->full_address) ? '' : $entity->full_address !!}">
                    {!! empty($entity->full_address) ? '' : '<i class="fa fa-map-marker"
                                                                                                  aria-hidden="true"></i>'.$entity->full_address !!}</a>

            </td>
            <td class="text-center text-middle">{!! $entity->active == 1 ? '<i class="fa fa-check" aria-hidden="true"></i>':'' !!}</td>

        </tr>
    @endforeach
@endif --}}