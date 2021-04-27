@if(!$entities->total())
    <div class="empty-box"><span>Không thể tìm thấy dữ liệu trên chương trình</span><span>Không thể tìm thấy dữ liệu trên chương trình</span>
    </div>
@else
    @foreach($entities as $index=>$entity)
        <tr class="{{$index %2 != 0 ? 'even' :'odd'}}">
            <td class="text-middle" data-name="true">
                {{$entity->username}}
            </td>
            <td class="text-middle">{{$entity->email}}</td>
            <td class="text-middle">{!!$entity->getRoleText()!!}</td>
            <td class="text-center text-middle">{{$entity->getDateTime('ins_date', 'd-m-Y H:i')}}</td>
            <td class="text-center text-middle">{{$entity->getDateTime('upd_date', 'd-m-Y H:i')}}</td>
            <td class="text-middle">{!! empty($entity->insUser) ? '' : $entity->insUser->username !!}</td>
            <td class="text-middle">{!! empty($entity->updUser) ? '' : $entity->updUser->username !!}</td>
        </tr>
    @endforeach
@endif