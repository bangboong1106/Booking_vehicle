@include('layouts.backend.elements.column_config._list',[
    'entity'=>'role',
    'is_action' => true,
    'is_show_history' => false,
    'attributes' => getColumnConfig("role"), 
    'configList'=> isset($configList) ? $configList : []])
{{-- @if(!$entities->total()|| $entities->total() ==0 )
    <div class="empty-box"><span><i>Không thể tìm thấy dữ liệu trên chương trình</i></span>
        <div class="wrap-btn">
            <div class="btn" id="btn-add-empty">
                <a href="{{backUrl($routePrefix.'.create') }}">
                    <i class="fa fa-plus"><span>Thêm vai trò</span></i>
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
                @can('edit role')
                    <a class="detail-toggle" href="#">{{$entity->title}}</a>
                @else
                    {{$entity->title}}
                @endcan
            </td>
            <td class="text-middle">{{$entity->name}}</td>
            <td class="text-middle">{{$entity->ins_date}}</td>
            <td class="text-middle">{{$entity->upd_date}}</td>
        </tr>
    @endforeach
@endif --}}