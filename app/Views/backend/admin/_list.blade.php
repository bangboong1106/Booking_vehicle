@include('layouts.backend.elements.column_config._list',[
    'entity'=>'admin',
    'is_action' => true,
    'is_show_history' => false,
    'attributes' => getColumnConfig("admin"), 
    'configList'=> isset($configList) ? $configList : []])
{{-- @if(!$entities->total())
    <div class="empty-box"><span><i>Không thể tìm thấy dữ liệu trên chương trình</i></span>
        <div class="wrap-btn">
            <div class="btn" id="btn-add-empty">
                <a href="{{backUrl($routePrefix.'.create') }}">
                    <i class="fa fa-plus"><span>Thêm người dùng</span></i>
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
                <a href="#" class="detail-toggle">
                    <div class="person-circle chr-{{strtoupper($entity->username[0])}}"
                         style="cursor: pointer;">{{strtoupper($entity->username[0])}}
                    </div>
                    {{$entity->username}}
                </a>
            </td>
            <td class="text-middle">
                {{$entity->full_name}}
            </td>
            <td class="text-middle"><a href="mailto:{{$entity->email}}"><i class="fa fa-envelope"
                                                                           aria-hidden="true"></i>{{$entity->email}}</a>
            </td>
            <td class="text-middle">{!! $entity->getRoleText() !!}</td>
            <td class="text-center text-middle">{{$entity->getDateTime('ins_date', 'd-m-Y H:i')}}</td>
            <td class="text-center text-middle">{{$entity->getDateTime('upd_date', 'd-m-Y H:i')}}</td>
        </tr>
    @endforeach
@endif --}}