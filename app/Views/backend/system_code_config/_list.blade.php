@include('layouts.system_code_config.elements.column_config._list',[
    'entity'=>'system_code_config',
    'is_action' => true,
    'is_show_history' => false,
    'attributes' => getColumnConfig("system_code_config"),
    'configList'=> isset($configList) ? $configList : []])
{{-- @if(!$entities->total())
    <div class="empty-box"><span><i>Không thể tìm thấy dữ liệu trên chương trình</i></span>
        <div class="wrap-btn">
            <div class="btn" id="btn-add-empty">
                <a href="{{backUrl($routePrefix.'.create') }}">
                    <i class="fa fa-plus"><span>Thêm mã hệ thống</span></i>
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
            <td>{{$entity->getSystemCodeTypeText()}}</td>
            <td>{{$entity->prefix}}</td>
            <td>{{$entity->suffix_length}}</td>
            <td>{{$entity->prefix.sprintf('%0' . $entity->suffix_length . 'd', 1)}}</td>

        </tr>
    @endforeach
@endif --}}