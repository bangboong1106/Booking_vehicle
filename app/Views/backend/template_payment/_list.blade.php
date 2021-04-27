@include('layouts.backend.elements.column_config._list',[
    'entity'=>'template_payment',
    'is_action' => false,
    'is_show_history' => false,
    'attributes' => getColumnConfig("template_payment"),
    'configList'=> isset($configList) ? $configList : []])
{{-- @if(!$entities->total()|| $entities->total() ==0 )
    <div class="empty-box"><span><i>Không thể tìm thấy dữ liệu trên chương trình</i></span>
        <div class="wrap-btn">
            <div class="btn" id="btn-add-empty">
                <a href="{{backUrl($routePrefix.'.create') }}">
                    <i class="fa fa-plus"><span>Thêm biểu mẫu chi phí</span></i>
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
                    {{$entity->title}}
                </a>
            </td>
            <td class="text-middle">{!! $entity->description !!}</td>
            <td class="text-middle">{!! $entity->matching_column_index !!}</td>
            <td class="text-middle">{!! $entity->header_row_index !!}</td>
            <td class="text-middle text-center">
                @if(isset($entity) && isset($entity->tryGet('getFile')->file_id))
                    <a class="fa fa-download" href="#"
                       data-url="{{ route('file.downloadFile', $entity->tryGet('getFile')->file_id) }}"></a>
                @endif
            </td>
            <td class="text-middle text-center">{{$entity->upd_date}}</td>
        </tr>

    @endforeach
@endif --}}