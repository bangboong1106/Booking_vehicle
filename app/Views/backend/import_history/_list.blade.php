@include('layouts.backend.elements.column_config._list',[
    'entity'=>'import_history',
    'is_action' => false,
    'is_show_history' => false,
    'attributes' => getColumnConfig("import_history"),
    'configList'=> isset($configList) ? $configList : []])
{{-- @if(!$entities->total())
    <div class="empty-box"><span><i>Không thể tìm thấy dữ liệu trên chương trình</i></span>
        <div class="wrap-btn">
        </div>
    </div>
@else
    @foreach($entities as $index=>$entity)
        <tr class="{{$index %2 != 0 ? 'even' :'odd'}}">
            <td class="text-center text-middle list-action">
                <a href="{{ route('file.downloadFile', $entity->file_id) }}">
                    <i class="fa fa-download"></i>
                </a>
            </td>
            <td class="text-middle">{{$entity->file_name}}</td>
            <td class="text-middle">{{$entity->module}}</td>
            <td class="text-middle">{{ $entity->type == 'update' ? trans('models.import_history.attributes.type_update') :
                trans('models.import_history.attributes.type_create') }}</td>
            <td class="text-center">{{$entity->success_record}}</td>
            <td class="text-center">{{$entity->error_record}}</td>
            <td class="text-center">{{$entity->username}}</td>
            <td class="text-center">{{$entity->ins_date}}</td>
        </tr>
    @endforeach
@endif --}}
