@foreach($entities as $index=>$entity)
    <tr class="{{$index %2 != 0 ? 'even' :'odd'}}">
        @include('layouts.backend.elements.list_to_checkbox', ['id' => $entity->id])
        @include('layouts.backend.elements.list_to_action')
        <td class="text-middle" data-name="true">
            <a href="{{backUrl('district.edit', ['id' => $entity->id, 'backUrlKey' => isset($backUrlKey) ? $backUrlKey : null])}}">{{$entity->district_id}}</a>
        </td>
        <td class="text-middle">
            <a href="{{backUrl('district.edit', ['id' => $entity->id, 'backUrlKey' => isset($backUrlKey) ? $backUrlKey : null])}}">{{$entity->title}}</a>
        </td>
        <td class="text-middle">{!! $entity->getType() !!}</td>
        <td class="text-middle">{!! $entity->tryGet('province')->title !!}</td>
        <td class="text-middle text-center">{{$entity->ins_date}}</td>
        <td class="text-middle text-center">{{$entity->upd_date}}</td>
    </tr>
@endforeach