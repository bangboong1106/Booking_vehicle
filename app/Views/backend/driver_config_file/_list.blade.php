@include('layouts.system_code_config.elements.column_config._list',[
    'entity'=>'driver_config_file',
    'is_action' => true,
    'is_show_history' => false,
    'attributes' => getColumnConfig("driver_config_file"),
    'configList'=> isset($configList) ? $configList : []])
{{-- @foreach($entities as $index=>$entity)
    <tr class="{{$index %2 != 0 ? 'even' :'odd'}} {{isset($selectedItem) && in_array($entity->id, $selectedItem) ?
        'row-selected' : '' }}" data-id="{{$entity->id}}">
        @include('layouts.backend.elements.list_to_checkbox', ['id' => $entity->id])
        @include('layouts.backend.elements.list_to_action')
        <td class="text-middle"><a
                    href="{{backUrl('driver-config-file.edit', ['id' => $entity->id, 'backUrlKey' => isset($backUrlKey) ? $backUrlKey : null])}}">{{$entity->file_name}}</a>
        </td>
        <td class="text-middle">{{$entity->getFileType()}}</td>
        <td class="text-middle">
        {!! $entity->is_show_register == 1 ? '<i class="fa fa-check" aria-hidden="true"></i>':'' !!}
        <td class="text-middle">
        {!! $entity->is_show_expired == 1 ? '<i class="fa fa-check" aria-hidden="true"></i>':'' !!}
        <td class="text-middle">
        {!! $entity->active == 1 ? '<i class="fa fa-check" aria-hidden="true"></i>':'' !!}

    </tr>
@endforeach --}}