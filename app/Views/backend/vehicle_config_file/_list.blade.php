@include('layouts.backend.elements.column_config._list',[
    'entity'=>'vehicle_config_file',
    'is_action' => true,
    'is_show_history' => false,
    'attributes' => getColumnConfig("vehicle_config_file"),
    'configList'=> isset($configList) ? $configList : []])

{{-- @if(!$entities->total())
    <div class="empty-box"><span><i>Không thể tìm thấy dữ liệu trên chương trình</i></span>
        <div class="wrap-btn">
            <div class="btn" id="btn-add-empty">
                <a href="{{backUrl($routePrefix.'.create') }}">
                    <i class="fa fa-plus"><span>Thêm cấu hình xe</span></i>
                </a>
            </div>
        </div>
    </div>
@else
    @foreach($entities as $index=>$entity)
        @if($entity->is_required == 0)
            <tr class="{{$index %2 != 0 ? 'even' :'odd'}} {{isset($selectedItem) && in_array($entity->id, $selectedItem) ?
                'row-selected' : '' }}" data-id="{{$entity->id}}">
                @include('layouts.backend.elements.list_to_checkbox', ['id' => $entity->id])
                @include('layouts.backend.elements.list_to_action')
                <td class="text-middle">
                    <a href="{{backUrl('vehicle-config-file.edit', ['id' => $entity->id, 'backUrlKey' => isset($backUrlKey) ? $backUrlKey : null])}}">{{$entity->file_name}}</a>
                </td>
                <td class="text-middle">{{$entity->getFileType()}}</td>
                <td class="text-middle">
                {!! $entity->is_show_expired == 1 ? '<i class="fa fa-check" aria-hidden="true"></i>':'' !!}
                <td class="text-middle">
                {!! $entity->is_show_expired == 1 ? '<i class="fa fa-check" aria-hidden="true"></i>':'' !!}
                <td class="text-middle">
                {!! $entity->active == 1 ? '<i class="fa fa-check" aria-hidden="true"></i>':'' !!}
            </tr>
        @endif
    @endforeach
@endif --}}