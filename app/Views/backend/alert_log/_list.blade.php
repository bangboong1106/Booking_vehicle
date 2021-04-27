@if(!$entities->total())
    <div class="empty-box"><span>Không thể tìm thấy dữ liệu trên chương trình</span></div>
@else
    @foreach($entities as $index=>$entity)
        <tr class="{{$index %2 != 0 ? 'even' :'odd'}} {{isset($selectedItem) && in_array($entity->id, $selectedItem) ?
            'row-selected' : '' }}" data-id="{{$entity->id}}">
            @include('layouts.backend.elements.list_to_checkbox', ['id' => $entity->id])
            @include('layouts.backend.elements.list_to_action')
            <td class="text-middle">
                <a class="detail-toggle" href="#">
                    {{$entity->name}}
                </a>
            </td>
            <td class="text-middle">{{$entity->title}}</td>
            <td class="text-middle">{!! $entity->getAlertType()!!}</td>
            <td class="text-middle">{{$entity->ins_date}}</td>
            <td class="text-middle">{{$entity->upd_date}}</td>

        </tr>
    @endforeach
@endif