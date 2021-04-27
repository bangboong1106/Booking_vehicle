@foreach($entities as $index=>$entity)
    <tr class="{{$index %2 != 0 ? 'even' :'odd'}}">
        <td class="text-middle" data-name="true">
            <a href="{{backUrl('vehicle-group.edit', ['id' => $entity->id, 'backUrlKey' => isset($backUrlKey) ? $backUrlKey : null])}}">{{$entity->name}}</a>
        </td>
        <td class="text-middle">{{$entity->ins_date}}</td>
        <td class="text-middle">{{$entity->upd_date}}</td>
        <td class="text-middle text-center">
            @include('layouts.backend.elements.list_delete_btn')
        </td>
    </tr>
@endforeach